<?php
namespace TruFetcher\Includes\Api\Controllers\Admin;

use TruFetcher\Includes\Api\Pagination\Tru_Fetcher_Api_Pagination;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Posts_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_Response;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;

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
class Tru_Fetcher_Api_Admin_Posts_Controller extends Tru_Fetcher_Api_Admin_Base_Controller
{

    private string $namespace = "/posts";
    private string $protectedEndpoint;

    private Tru_Fetcher_Api_Admin_Posts_Response $apiPostResponse;

    public function __construct()
    {
        parent::__construct();
        $this->protectedEndpoint = $this->apiConfigEndpoints->adminNamespace . $this->namespace;
    }

    public function init()
    {
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }


    private function loadResponseObjects()
    {
        $this->apiPostResponse = new Tru_Fetcher_Api_Admin_Posts_Response();
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->adminNamespace, '/posts', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "postsHandler"],
            'permission_callback' => [$this->apiAuth, "tokenRequestHandler"]
        ));
    }

    public function postsHandler(\WP_REST_Request $request)
    {
        $defaultPostType = 'post';
        $postType = $request->get_param('post_type');

        $postsPerPage = 10;
        $pageNumber = 1;
        if (empty($postType)) {
            $postType = $defaultPostType;
        }
        if (isset($request["page_number"])) {
            $pageNumber = (int)$request["page_number"];
        }
        if (isset($request["posts_per_page"])) {
            $postsPerPage = (int)$request["posts_per_page"];
        }
        $args = [
            'orderby' => 'date',
            'order' => 'DESC',
            'post_type' => $postType,
        ];

        $offset = $this->calculateOffset($pageNumber, $postsPerPage);
        $offsetArgs = [
            'posts_per_page' => $postsPerPage,
            'offset' => $offset,
        ];

        $allPostsQuery = new \WP_Query($args);
        $postQuery = new \WP_Query(array_merge($args, $offsetArgs));

        $this->apiPostResponse->setPosts($this->buildPostsArray($postQuery->posts));
        $this->apiPostResponse->setPagination(
            Tru_Fetcher_Posts::getPostPagination($postQuery, $allPostsQuery, $offset, $postsPerPage)
        );
        return $this->controllerHelpers->sendSuccessResponse(
          'Posts fetched successfully',
            $this->apiPostResponse
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
//                "post_template_category" => get_field("post_template_category", $post->ID)
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
