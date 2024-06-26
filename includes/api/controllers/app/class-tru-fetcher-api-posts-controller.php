<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_List_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_Response;
use TruFetcher\Includes\Constants\Tru_Fetcher_Constants_Api;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Keymaps;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy;

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
    private Tru_Fetcher_Posts $postHelpers;
    private Tru_Fetcher_Api_Post_Response $apiPostResponse;
    private Tru_Fetcher_Api_Post_List_Response $postListResponse;
    private Tru_Fetcher_Api_Helpers_Keymaps $keymapHelpers;

    public function __construct()
    {
        parent::__construct();
        $this->apiConfigEndpoints->endpointsInit('/posts');
        $this->keymapHelpers = new Tru_Fetcher_Api_Helpers_Keymaps();
    }

    public function init()
    {
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }


    private function loadResponseObjects()
    {
        $this->apiPostResponse = new Tru_Fetcher_Api_Post_Response();
        $this->postListResponse = new Tru_Fetcher_Api_Post_List_Response();
        $this->postHelpers = new Tru_Fetcher_Posts();
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/post/(?<post_slug>[\w-]+)', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "postWithTemplateRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/template', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "postTemplateRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/post/(?<post_id>[\d]+)', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "singlePost"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/post/(?<post_id>[_\d\w-]+)/type/(?<post_type>[\w-]+)', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "singlePostType"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/list', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "postListRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/category/list', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "categoryListRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
    }

    public function singlePostType(\WP_REST_Request $request)
    {
        $postId = $request->get_param("post_id");

        $postType = $request->get_param("post_type");
        if (empty($postType)) {
            return $this->controllerHelpers->sendErrorResponse(
                'post_type_fetch_error',
                "Post Type not specified",
                $this->apiPostResponse
            );
        }
        $postsClass = new Tru_Fetcher_Posts();
        if (is_numeric($postId)) {
            $post = $postsClass->getPostTypePostById($postId, $postType);
        } else {
            $post = $postsClass->getPostTypePostByName($postId, $postType);
        }
        if (is_wp_error($post)) {
            return $this->controllerHelpers->sendErrorResponse(
                'post_fetch_error',
                "Post fetch error",
                $this->apiPostResponse
            );
        }
        $pageObject = Tru_Fetcher_Posts::buildPostObject($post);
        $this->apiPostResponse->setPost($pageObject);
        $this->apiPostResponse->setNavigation($this->postHelpers->getPostTypeNavigation($post));
//        $this->apiPostResponse->setProvider('internal');
        return $this->controllerHelpers->sendSuccessResponse(
            'Post type fetch',
            $this->apiPostResponse
        );
    }

    public function singlePost(\WP_REST_Request $request)
    {
        $postId = $request->get_param("post_id");
        $postsClass = new Tru_Fetcher_Posts();
        $post = $postsClass->getPostById($postId);
        if (is_wp_error($post)) {
            return $this->controllerHelpers->sendErrorResponse(
                'post_fetch_error',
                "Post fetch error",
                $this->apiPostResponse
            );
        }
        $pageObject = Tru_Fetcher_Posts::buildPostObject($post);
        $this->apiPostResponse->setPost($pageObject);
        return $this->sendResponse(
            "Post fetch successful",
            $this->apiPostResponse
        );
    }
    public function postWithTemplateRequestHandler(\WP_REST_Request $request)
    {
        $postSlug = $request->get_param("post_slug");

        $postsClass = new Tru_Fetcher_Posts();
        $post = $postsClass->getPostByName($postSlug);
        if (is_wp_error($post)) {
            return $this->sendResponse(
                "Post fetch error",
                $this->apiPostResponse
            );
        }
        $post = $this->postHelpers::buildPostObject($post);
        $postTemplate = $postsClass->getPostTemplateByPost($post);
        if (is_wp_error($postTemplate)) {
            return $this->controllerHelpers->sendErrorResponse(
                $postTemplate->get_error_code(),
                $postTemplate->get_error_message(),
                $this->apiPostResponse
            );
        }
        $this->apiPostResponse->setPost($post);
        $this->apiPostResponse->setTemplate($this->postHelpers::buildPostObject($postTemplate));

        $navigation = $this->postHelpers->getCategoryPostNavigation($post);
        if (is_wp_error($navigation)) {
            $this->apiPostResponse->addError($navigation);
        } else {
            $this->apiPostResponse->setNavigation($navigation);
        }

        return $this->controllerHelpers->sendSuccessResponse(
            'Post fetch successful',
            $this->apiPostResponse
        );
    }
    public function postTemplateRequestHandler(\WP_REST_Request $request)
    {
        $category = $request->get_param("category");
        $apiListingsServiceId = $request->get_param("api_listings_service");

        if (!empty($apiListingsServiceId)) {
            $findKeymap = $this->keymapHelpers->getKeymap((int)$apiListingsServiceId);
            $this->apiPostResponse->setKeymap(
                $this->keymapHelpers->flattenKeymap($findKeymap)
            );
        }
        $postsClass = new Tru_Fetcher_Posts();
        $postTemplate = $postsClass->getPostTemplate($category);
        if (is_wp_error($postTemplate)) {
            return $this->controllerHelpers->sendErrorResponse(
                $postTemplate->get_error_code(),
                $postTemplate->get_error_message(),
                $this->apiPostResponse
            );
        }
        $this->apiPostResponse->setTemplate($this->postHelpers::buildPostObject($postTemplate));

//        $navigation = $this->postHelpers->getCategoryPostNavigation($post);
//        if (is_wp_error($navigation)) {
//            $this->apiPostResponse->addError($navigation);
//        } else {
//            $this->apiPostResponse->setNavigation($navigation);
//        }

        return $this->controllerHelpers->sendSuccessResponse(
            'Post fetch successful',
            $this->apiPostResponse
        );
    }

    public function categoryListRequestHandler(\WP_REST_Request $request)
    {
        $args = [
            'post_type' => "post"
        ];
        $categoryList = [];
        foreach (get_categories() as $category) {
            $args["cat"] = $category->term_id;
            $getPosts = new \WP_Query($args);
            array_push($categoryList, [
                "category_name" => $category->name,
                "category_slug" => $category->slug,
                "total_posts" => $getPosts->post_count
            ]);
        }
        return $this->sendResponse(
            "Categories fetch successful",
            $categoryList
        );
    }

    public function postListRequestHandler(\WP_REST_Request $request)
    {
        $orderBy = $request->get_param("order_by");
        $order = $request->get_param("order");
        if (empty($orderBy)) {
            $orderBy = "date";
        }
        if (empty($order)) {
            $order = "desc";
        }
        $showAllCategories = true;
        $categories = [];

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
            'orderby' => $orderBy,
            'order' => $order,
            'post_type' => 'post',
            'meta_key' => '_thumbnail_id',
        ];

        $paginatedPostList = $this->postHelpers->getPaginatedPostList($args, $request);
        if (is_wp_error($paginatedPostList)) {
            return $this->controllerHelpers->sendErrorResponse(
                $paginatedPostList->get_error_code(),
                $paginatedPostList->get_error_message(),
                $this->postListResponse
            );
        }
        return $this->controllerHelpers->sendSuccessResponse(
            "Post list request success",
            $paginatedPostList
        );
    }

    private function sendResponse($message, $data)
    {
        $this->apiPostResponse->setStatus("success");
        $this->apiPostResponse->setMessage($message);
        $this->apiPostResponse->setData($data);
        return rest_ensure_response($this->apiPostResponse);
    }
}
