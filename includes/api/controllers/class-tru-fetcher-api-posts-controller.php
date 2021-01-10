<?php
Tru_Fetcher_Class_Loader::loadClass('includes/api/controllers/class-tru-fetcher-api-controller-base.php');

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
class Tru_Fetcher_Api_Posts_Controller extends Tru_Fetcher_Api_Controller_Base
{

    private string $namespace = "/posts";
    private string $publicEndpoint;
    private string $protectedEndpoint;

    private Tru_Fetcher_Api_Post_Response $apiPostResponse;

    public function __construct()
    {
        $this->publicEndpoint = $this->publicNamespace . $this->namespace;
        $this->protectedEndpoint = $this->protectedNamespace . $this->namespace;
    }

    public function init()
    {
        $this->load_dependencies();
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }

    private function load_dependencies()
    {
        Tru_Fetcher_Class_Loader::loadClassList([
            'includes/api/response/class-tru-fetcher-api-post-response.php',
        ]);
    }

    private function loadResponseObjects()
    {
        $this->apiPostResponse = new Tru_Fetcher_Api_Post_Response();
    }

    public function register_routes()
    {
        register_rest_route($this->publicEndpoint, '/list/request', array(
            'methods' => WP_REST_Server::CREATABLE,
            'callback' => [$this, "postListRequestHandler"],
            'permission_callback' => '__return_true'
        ));
    }

    public function postListRequestHandler(WP_REST_Request $request)
    {
        $postsPerPage = 10;
        $showAllCategories = true;
        $categories = [];
        $pageNumber = 1;
        if (isset($request["page_number"])) {
            $pageNumber = (int)$request["page_number"];
        }
        if (isset($request["posts_per_page"])) {
            $postsPerPage = (int)$request["posts_per_page"];
        }
        if (isset($request["show_all_categories"])) {
            $showAllCategories = $request["show_all_categories"];
        }
        if (isset($request["categories"])) {
            $categories = $request["categories"];
            if (is_array($categories) && count($categories) > 0) {
                $categories = implode(",", $categories);
            } else {
                $categories = 0;
            }
        }
        $args = [
            'cat' => $showAllCategories ? 0 : $categories,
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => 'post',
            'meta_key' => '_thumbnail_id',
        ];

        $offsetArgs = [
            'posts_per_page' => $postsPerPage,
            'offset' => $this->calculateOffset($pageNumber, $postsPerPage),
        ];

        $allPostsQuery = new WP_Query($args);
        $postQuery = new WP_Query(array_merge($args, $offsetArgs));
        $buildPostsArray = $this->buildPostsArray($postQuery->posts);
        return $this->sendResponse(
            "Post list request success",
            [
                "total_posts" => $allPostsQuery->post_count,
                "total_pages" => round($allPostsQuery->post_count / $postsPerPage),
                "posts" => $buildPostsArray,
            ]
        );
    }

    private function buildPostsArray($posts)
    {
        return array_map(function ($post) {
            return [
                "id" => $post->ID,
                "post_name" => $post->post_name,
                "post_title" => $post->post_title,
                "post_excerpt" => $post->post_excerpt,
                "post_modified" => $post->post_modified,
                "featured_image" => get_the_post_thumbnail_url($post),
                "post_category" => $this->buildTermsArray(get_the_category($post->ID)),
                "post_template_category" => get_field("post_template_category", $post->ID)
            ];
        }, $posts);
    }

    private function buildTermsArray($terms)
    {
        return array_map(function ($term) {
            return [
                "id" => $term->term_id,
                "name" => $term->name,
                "slug" => $term->slug
            ];
        }, $terms);
    }

    private function calculateOffset($pageNumber, $postsPerPage)
    {
        if ((int)$pageNumber === 1) {
            return 0;
        }
        return (int)$pageNumber * (int)$postsPerPage;
    }

    private function sendResponse($message, $data)
    {
        $this->apiPostResponse->setStatus("success");
        $this->apiPostResponse->setMessage($message);
        $this->apiPostResponse->setData($data);
        return rest_ensure_response($this->apiPostResponse);
    }
}
