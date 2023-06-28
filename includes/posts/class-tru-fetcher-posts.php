<?php
namespace TruFetcher\Includes\Posts;

use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use WP_Error;
use WP_Post;
use WP_Query;

/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Posts {

	public function __construct() {
		$this->loadDependencies();
	}

	private function loadDependencies() {

	}
    public function getTemplate( $categoryName, $taxonomyName, $postType ) {
        $category = get_term_by( "slug", $categoryName, $taxonomyName );
        if ( ! $category ) {
            return new WP_Error('request_invalid_parameters', sprintf(
                "Category not found for: Taxonomy [%s], Category [%s], Post Type [%s].",
                $taxonomyName, $categoryName, $postType
            ));
        }

        $args            = [
            'post_type'   => $postType,
            'numberposts' => 1,
            'tax_query'   => [
                [
                    'taxonomy' => $taxonomyName,
                    'field'    => 'term_id',
                    'terms'    => $category->term_id,
                ]
            ]
        ];
        $getPageTemplate = get_posts( $args );
        if (count($getPageTemplate) ===  0) {
            return new WP_Error(
                'page_not_found',
                sprintf(
                    "Page template not found for: Taxonomy [%s], Category [%s], Post Type [%s].",
                    $taxonomyName, $category->name, $postType
                )
            );
        }
        return $getPageTemplate[0];
    }

    public function getPostTemplateByPost(\WP_Post $post) {
	    $getPostCategory = get_field("post_template_category", $post->ID);
        if ($getPostCategory === null || !$getPostCategory) {
            return new WP_Error("post_category_not_set", "Post category not set.");
        }

        $getPostTemplate = get_posts([
            'numberposts'      => 1,
            'post_type'        => 'post_templates',
            "cat" => $getPostCategory->term_id
        ]);

        if (count($getPostTemplate) === 0) {
            return new WP_Error("post_template_not_found",
                sprintf("Page template not found for post name [%s] - category name [%s].", $post->post_title, $getPostCategory->slug));
        }

        return $getPostTemplate[0];
    }

    public static function isHomePage($pageId) {
        return $pageId === get_option( "page_on_front" );
    }

    public function getPageBySlug( ?string $slug = 'home' ) {
        if ( !$slug || $slug === "home" ) {
            $pageId  = get_option( "page_on_front" );
            $getPage = get_post( $pageId );
            if (empty($getPage)) {
                return new WP_Error("page_error", "Home page does not exist.");
            }
            return $getPage;
        }
        $getPage = get_page_by_path($slug);
        if (empty($getPage)) {
            return new WP_Error("page_error", sprintf("Page %s does not exist.", $slug));
        }

        return $getPage;
    }


    public function getCategoryPostNavigation($postName) {
	    $getPost = $this->getPostByName($postName);
        if (is_wp_error($getPost)) {
            return $getPost;
        }

        $categoryPosts = $this->getCategoryPosts($getPost);
        if (is_wp_error($categoryPosts)) {
            return $categoryPosts;
        }

        $postPosition = $this->getCurrentPostArrayPosition($getPost, $categoryPosts);
        if ($postPosition === false) {
            return new WP_Error(
                'post_position_error',
                sprintf("Post (%s) position not found.", $postName)
            );
        }

        $prevPost = false;
        $nextPost = false;

        if ($postPosition > 0 && isset($categoryPosts[(int)$postPosition - 1])) {
            $prevPost = $categoryPosts[(int)$postPosition - 1];
        }
        if (isset($categoryPosts[(int)$postPosition + 1])) {
            $nextPost = $categoryPosts[(int)$postPosition + 1];
        }
        return [
            "prev_post" => $prevPost,
            "next_post" => $nextPost
        ];
    }

    private function getCurrentPostArrayPosition(WP_Post $currentPost, $postsArray) {
        foreach ($postsArray as $key => $post) {
            if ($currentPost->ID === $post->ID) {
                return $key;
            }
        }
        return false;
    }

    public function getPostByName($postName) {
        $getPost = get_posts([
            'numberposts'      => 1,
            'post_type'        => 'post',
            "name" => $postName
        ]);
        if (count($getPost) === 0) {
            return new WP_Error(
                'post_not_found',
                sprintf("Post (%s) not found.", $postName)
            );
        }
        return $getPost[0];
    }

    private function getCategoryPosts(WP_Post $post) {
        $category = get_field("post_template_category", $post->ID);

        if (!$category) {
            return new WP_Error(
                'category_not_found',
                "Post template category not found."
            );
        }
        $args = array(
            'numberposts'	=> -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type'		=> 'post',
            'meta_key'		=> 'post_template_category',
            'meta_value'	=> $category->term_id
        );

        $getCategoryPosts = new WP_Query( $args );

        if (count($getCategoryPosts->posts) === 0) {
            return new WP_Error(
                'category_posts_not_found',
                sprintf("Posts not found for category (%s)", $category->name)
            );
        }
        return $getCategoryPosts->posts;
    }

    public static function getPageOptions( $page ) {
        $options = [];
        $pageTypeMetaField = (new Tru_Fetcher_Meta_Fields_Page_Options())->getField(
            Tru_Fetcher_Meta_Fields_Page_Options::META_KEY_PAGE_TYPE
        );
        $options['pageType'] = null;
        if (isset($pageTypeMetaField['meta_key'])) {
            $options['pageType'] = get_post_meta($page->ID, $pageTypeMetaField['meta_key'], true);
        }
        if (!$options['pageType']) {
            $options['pageType'] = 'general';
        }
        return $options;
    }
}
