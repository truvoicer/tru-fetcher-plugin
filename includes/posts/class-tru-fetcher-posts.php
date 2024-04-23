<?php

namespace TruFetcher\Includes\Posts;

use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_List_Response;
use TruFetcher\Includes\Constants\Tru_Fetcher_Constants_Api;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Base;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Api\Pagination\Tru_Fetcher_Api_Pagination;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Post;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Post_Tpl;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Category;
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
class Tru_Fetcher_Posts
{

    private Tru_Fetcher_Taxonomy $taxonomyHelpers;
    private Tru_Fetcher_Meta_Fields_Post_Options $postOptions;
    public function __construct()
    {
        $this->taxonomyHelpers = new Tru_Fetcher_Taxonomy();
        $this->postOptions = new Tru_Fetcher_Meta_Fields_Post_Options();
    }

    const DEFAULT_POST_LIST_ATTS = [
        "ID",
        "post_name",
        "post_title",
        "post_excerpt",
        "post_modified",
        "featured_image",
        "post_category",
        "post_template_category",
    ];

    public static function buildPostObject(WP_Post $post, ?array $fields = [])
    {
        $postTypes = new Tru_Fetcher_Post_Types();
        $buildPost = clone $post;
        $buildPost->seo_title = $post->post_title . " - " . get_bloginfo('name');
        $buildPost = $postTypes->buildPostTypeData($post);

        $buildPost->post_content = apply_filters("the_content", $post->post_content);
        if (count($fields) > 0) {
            $buildPost = (object)array_intersect_key((array)$post, array_flip($fields));
            if (in_array('url', $fields)) {
                $buildPost->url = get_page_uri($post);
            }
        }
        if (!count($fields) || in_array("post_category", $fields)) {
            $buildPost->categories = Tru_Fetcher_Taxonomy::getPostCategories($post, ["term_id", "name", "slug"]);
        }
        if (!count($fields) || in_array("post_template_category", $fields)) {
            $buildPost->post_template_category = null;
        }
        if (!count($fields) || in_array("featured_image", $fields)) {
            $buildPost->featured_image = get_the_post_thumbnail_url($post);
        }
        $buildPost->provider = 'internal';
        return $buildPost;
    }

    public function getPostTypePostById(int $postId, string $postType)
    {
        $args = [
            'post_type' => $postType,
            'numberposts' => 1,
            'p' => $postId
        ];
        return $this->findPostByArgs($args);
    }
    public function getPostTypePostByName(string $postName, string $postType)
    {
        $args = [
            'post_type' => $postType,
            'numberposts' => 1,
            'name' => $postName
        ];
        return $this->findPostByArgs($args);
    }

    public function findPostByArgs(array $args) {
        $getPageTemplate = get_posts($args);
        if (count($getPageTemplate) === 0) {
            return new WP_Error(
                'post_not_found',
                sprintf(
                    "Post not found for: args [%s].",
                    json_encode($args)
                )
            );
        }
        return $getPageTemplate[0];
    }

    public function getTemplate($categoryName, $taxonomyName, $postType)
    {
        $category = get_term_by("slug", $categoryName, $taxonomyName);
        if (!$category) {
            return new WP_Error('request_invalid_parameters', sprintf(
                "Category not found for: Taxonomy [%s], Category [%s], Post Type [%s].",
                $taxonomyName, $categoryName, $postType
            ));
        }

        $args = [
            'post_type' => $postType,
            'numberposts' => 1,
            'tax_query' => [
                [
                    'taxonomy' => $taxonomyName,
                    'field' => 'term_id',
                    'terms' => $category->term_id,
                ],
            ]
        ];
        $getPageTemplate = get_posts($args);
        if (count($getPageTemplate) > 0) {
            return $getPageTemplate[0];
        }
        $uncategorisedCategory = get_term_by(
            "slug",
            "uncategorised",
            Tru_Fetcher_Taxonomy_Category::NAME
        );
        if (is_wp_error($uncategorisedCategory)) {
            return $uncategorisedCategory;
        }
        $args['tax_query'] = [
            [
                'taxonomy' => Tru_Fetcher_Taxonomy_Category::NAME,
                'field' => 'term_id',
                'terms' => $uncategorisedCategory->term_id,
            ],
        ];
        $getPageTemplate = get_posts($args);
        if (count($getPageTemplate) > 0) {
            return $getPageTemplate[0];
        }
        return new WP_Error(
            'page_not_found',
            sprintf(
                "Page template not found for: Taxonomy [%s], Category [%s, uncategorised], Post Type [%s].",
                $taxonomyName, $category->name, $postType
            )
        );
    }

    public function getPostTemplateCategory(\WP_Post $post) {
        $postTemplateCategory= null;

        $postTemplateCategoryMetaKey = $this->postOptions->getMetaKey($this->postOptions::META_KEY_POST_TEMPLATE_CATEGORY);
        if (
            !empty($postTemplateCategoryMetaKey) &&
            property_exists($post, "post_options") &&
            !empty($post->post_options[$postTemplateCategoryMetaKey])
        )
        {
            $postTemplateCategory = $post->post_options[$postTemplateCategoryMetaKey];
        }

        if (!empty($postTemplateCategory)) {
            $categoryId = (int)$postTemplateCategory;
            $category = (new Tru_Fetcher_Taxonomy())->fetchTerm($categoryId, Tru_Fetcher_Taxonomy_Category::NAME);
            if (is_wp_error($category)) {
                return $category;
            }
            $source = Tru_Fetcher_Taxonomy_Category::NAME;
        } elseif (
            property_exists($post, "categories") &&
            is_array($post->categories) &&
            count($post->categories) > 0
        ) {
            $category = $post->categories[0];
            $source = $postTemplateCategoryMetaKey;
        } else {
            return new WP_Error(
                "post_template_category_not_found",
                sprintf("Post template category not found or category not set for post name [%s].", $post->post_title)
            );
        }
        return [
            'source' => $source,
            'category' => $category
        ];
    }

    public function getPostTemplate(?string $category)
    {
        if (!$category) {
            return $this->getUncategorisedPostTemplate();
        }
        $findTerm = get_term_by("slug", $category, Tru_Fetcher_Taxonomy_Category::NAME);
        if (is_wp_error($findTerm)) {
            return $findTerm;
        }
        if (!$findTerm) {
            return $this->getUncategorisedPostTemplate();
        }
        $getPostTemplate = get_posts([
            'numberposts' => 1,
            'post_type' => Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
            "cat" => $findTerm->term_id
        ]);
        if (count($getPostTemplate) > 0) {
            return $getPostTemplate[0];
        }
        return $this->getUncategorisedPostTemplate();
    }

    public function getUncategorisedPostTemplate()
    {
        $uncategorisedCategory = get_term_by(
            "slug",
            "uncategorised",
            Tru_Fetcher_Taxonomy_Category::NAME);
        if (is_wp_error($uncategorisedCategory)) {
            return $uncategorisedCategory;
        }

        $getPostTemplate = get_posts([
            'numberposts' => 1,
            'post_type' => Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
            "cat" => $uncategorisedCategory->term_id
        ]);
        if (count($getPostTemplate) > 0) {
            return $getPostTemplate[0];
        }
        return new WP_Error(
            "post_template_not_found",
            sprintf(
                "Page template not found for uncategorized category. Please add a post template for this category."
            )
        );
    }

    public function getPostTemplateByPost(\WP_Post $post)
    {
        $category = $this->getPostTemplateCategory($post);
        if (is_wp_error($category)) {
            return $category;
        }
        $category = $category['category'];
        $getPostTemplate = get_posts([
            'numberposts' => 1,
            'post_type' => Tru_Fetcher_Post_Types_Trf_Post_Tpl::NAME,
            "cat" => $category->term_id
        ]);

        if (count($getPostTemplate) > 0) {
            return $getPostTemplate[0];
        }

        $uncategorisedTemplate = $this->getUncategorisedPostTemplate();
        if (is_wp_error($uncategorisedTemplate)) {
            return new WP_Error(
                "post_template_not_found",
                sprintf(
                    "Page template not found for post name [%s] - category name [%s]. Please add a post template for this category or a catchall template for uncategorized.",
                    $post->post_title,
                    $category->slug
                )
            );
        }
        return $uncategorisedTemplate;
    }

    public static function isHomePage($pageId)
    {
        return $pageId === get_option("page_on_front");
    }

    public function getPageBySlug(?string $slug = 'home')
    {
        if (!$slug || $slug === "home") {
            $pageId = get_option("page_on_front");
            $getPage = get_post($pageId);
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


    public function getCategoryPostNavigation(\WP_Post $post)
    {
        $categoryPosts = $this->getCategoryPosts($post);
        if (is_wp_error($categoryPosts)) {
            return $categoryPosts;
        }
        return $this->getPostNavigation($post, $categoryPosts);
    }


    public function getPostNavigation(\WP_Post $post, array $postList)
    {
        $postPosition = $this->getCurrentPostArrayPosition($post, $postList);
        if ($postPosition === false) {
            return new WP_Error(
                'post_position_error',
                sprintf("Post (%s) position not found.", $post->post_title)
            );
        }

        $prevPost = false;
        $nextPost = false;

        if ($postPosition > 0 && isset($postList[(int)$postPosition - 1])) {
            $prevPost = $postList[(int)$postPosition - 1];
        }
        if (isset($postList[(int)$postPosition + 1])) {
            $nextPost = $postList[(int)$postPosition + 1];
        }
        return [
            "prev_post" => ($prevPost instanceof \WP_Post)? $this::buildPostObject($prevPost) : $prevPost,
            "next_post" => ($nextPost instanceof \WP_Post)? $this::buildPostObject($nextPost) : $nextPost
        ];
    }
    public function getPostTypeNavigation(\WP_Post $post)
    {
        switch ($post->post_type) {
            case Tru_Fetcher_Post_Types_Page::NAME:
            case Tru_Fetcher_Post_Types_Post::NAME:
                return $this->getCategoryPostNavigation($post);
        }

        $args = array(
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => $post->post_type,
        );
        $posts = new WP_Query($args);
        return $this->getPostNavigation($post, $posts->posts);
    }

    private function getCurrentPostArrayPosition(WP_Post $currentPost, $postsArray)
    {
        foreach ($postsArray as $key => $post) {
            if ($currentPost->ID === $post->ID) {
                return $key;
            }
        }
        return false;
    }

    public function getPostByName($postName)
    {
        $getPost = get_posts([
            'numberposts' => 1,
            'post_type' => 'post',
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

    public function getPostById($postName)
    {
        $getPost = get_posts([
            'numberposts' => 1,
            'post_type' => 'post',
            "p" => $postName
        ]);
        if (count($getPost) === 0) {
            return new WP_Error(
                'post_not_found',
                sprintf("Post (%s) not found.", $postName)
            );
        }
        return $getPost[0];
    }

    private function getCategoryPosts(WP_Post $post)
    {
        $categoryData = $this->getPostTemplateCategory($post);
        $category = $categoryData['category'];
        if (is_wp_error($category)) {
            return $category;
        }
        $args = array(
            'numberposts' => -1,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'post',
        );

        $postTemplateCategoryMetaKey = $this->postOptions->getMetaKey($this->postOptions::META_KEY_POST_TEMPLATE_CATEGORY);

        $taxQuery = [
            [
                'taxonomy' => Tru_Fetcher_Taxonomy_Category::NAME,
                'field' => 'term_id',
                'terms' => array($category->term_id),
                'operator' => 'IN',
            ],
            'relation' => 'OR',
        ];

        $metaQuery = [
            [
                'key' => $postTemplateCategoryMetaKey,
                'value' =>  $category->term_id,
                'compare' => '=',
            ],
            'relation' => 'OR',
        ];

        if (
            $categoryData['source'] === $postTemplateCategoryMetaKey &&
            !empty($postTemplateCategoryMetaKey)
        ) {
            $args['meta_query'] = $metaQuery;
        }
        $getCategoryPosts = null;
        if (!empty($args['meta_query']) ) {
            $getCategoryPosts = new WP_Query($args);
        }

        if ($getCategoryPosts instanceof \WP_Query && count($getCategoryPosts->posts) > 0) {
            return $getCategoryPosts->posts;
        }
        unset($args['meta_query']);
        $args['tax_query'] = $taxQuery;
        $getCategoryPosts = new WP_Query($args);
        if (count($getCategoryPosts->posts) === 0) {
            return new WP_Error(
                'category_posts_not_found',
                sprintf("Posts not found for category (%s)", $category->name)
            );
        }
        return $getCategoryPosts->posts;
    }

    public static function getPostMetaFields(WP_Post $page)
    {
        $data = [];
        foreach (Tru_Fetcher_Meta_Fields::META_FIELDS as $metaFieldClass) {
            $metaField = new $metaFieldClass();
            $postType = $metaField->getPostType();
            if ($page->post_type !== $postType) {
                continue;
            }
            $data = array_merge(
                $data,
                $metaField->buildPostMetaFieldsData($page)
            );
        }
        return $data;
    }

    public static function getPagination(int $total, int $offset, int $perPage, ?int $pageNumber = null)
    {
        $pagination = new Tru_Fetcher_Api_Pagination();
        $pageCount = ceil($total / $perPage);
        if ($offset > $total) {
            $offset = $total;
        }

        if ($pageNumber === null) {
            if ($offset === 0) {
                $pageNumber = 1;
            } else {
                $offsetPageCount = floor($total - $offset);
                $pageNumber = floor($pageCount - floor($offsetPageCount / $perPage));
            }
        }

        $pagination->setPageCount($pageCount);
        $pagination->setPageNumber($pageNumber);
        $pagination->setOffset($offset);
        $pagination->setPageSize($perPage);
        $pagination->setTotalItems($total);
        $pagination->setCurrentPerPage($perPage);
        $pagination->setTotalPages($pageCount);
        $pagination->setLastPage($pageCount);
        return $pagination;
    }

    public static function getPostPagination(\WP_Query $postsQuery, \WP_Query $totalPostsQuery, int $offset, int $perPage, ?int $pageNumber = null)
    {
        $pagination = new Tru_Fetcher_Api_Pagination();
        $total = $totalPostsQuery->found_posts;
        $pageCount = $postsQuery->max_num_pages;
        if ($offset > $total) {
            $offset = $total;
        }

        if ($pageNumber === null) {
            if ($offset === 0) {
                $pageNumber = 1;
            } else {
                $offsetPageCount = floor($total - $offset);
                $pageNumber = floor($pageCount - floor($offsetPageCount / $perPage));
            }
        }

        $pagination->setPageCount($pageCount);
        $pagination->setTotalPages($pageCount);
        $pagination->setPageNumber($pageNumber);
        $pagination->setOffset($offset);
        $pagination->setPageSize($perPage);
        $pagination->setTotalItems($total);
        $pagination->setCurrentPerPage(count($postsQuery->get_posts()));
        return $pagination;
    }

    public function getPaginationRequestData(\WP_REST_Request $request) {

        $postsPerPage = 10;
        $pageNumber = 1;
        $offset = 0;

        $paginationType = $request->get_param(Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGINATION_TYPE']);
        if (empty($paginationType)) {
            return new WP_Error(
                'pagination_type_error',
                "Pagination type not specified"
            );
        }

        if (isset($request[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']])) {
            $postsPerPage = (int)$request[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']];
        }
        switch ($paginationType) {
            case 'offset':
                if (isset($request[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']])) {
                    $offset = (int)$request[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']];
                }
                break;
            case 'page':
                if (isset($request[Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGE_NUMBER']])) {
                    $pageNumber = (int)$request[Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGE_NUMBER']];
                }
                $offset = self::calculateOffset($pageNumber, $postsPerPage);
        }
        return [
            Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGINATION_TYPE'] => $paginationType,
            Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE'] => $postsPerPage,
            Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGE_NUMBER'] => $pageNumber,
            Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET'] => $offset
        ];
    }

    public static function calculateOffset($pageNumber, $postsPerPage)
    {
        if ((int)$pageNumber === 1) {
            return 0;
        }
        return  (int)$postsPerPage * ((int)$pageNumber - 1);
    }



    public function buildPostsArray(array $posts, ?array $fields = self::DEFAULT_POST_LIST_ATTS)
    {
        return array_map(function (WP_Post $post) use ($fields) {
            return self::buildPostObject($post, $fields);
        }, $posts);
    }

    public function getPaginatedPostList(array $args, \WP_REST_Request $request) {
        $postListResponse = new Tru_Fetcher_Api_Post_List_Response();

        $paginationRequestData = $this->getPaginationRequestData($request);
        if (is_wp_error($paginationRequestData)) {
            return $paginationRequestData;
        }

        $offsetArgs = [
            'posts_per_page' => $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']],
            'offset' => $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']],
        ];

        $allPostsQuery = new \WP_Query($args);
        $postQuery = new \WP_Query(array_merge($args, $offsetArgs));

        $pagination = Tru_Fetcher_Posts::getPostPagination(
            $postQuery,
            $allPostsQuery,
            $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']],
            $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']]
        );
        $pagination->setPaginationType($paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGINATION_TYPE']]);
        $postListResponse->setPagination($pagination);
        $postListResponse->setPostList($this->buildPostsArray($postQuery->posts));

        return $postListResponse;
    }
}
