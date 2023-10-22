<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_List_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_Response;
use TruFetcher\Includes\Constants\Tru_Fetcher_Constants_Api;
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

    public function __construct()
    {
        parent::__construct();
        $this->apiConfigEndpoints->endpointsInit('/posts');
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
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "postWithTemplateRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/post/(?<post_id>[\d]+)', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "singlePost"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/post/(?<post_id>[\d]+)/type/(?<post_type>[\w-]+)', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "singlePostType"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/list/request', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "postListRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/list/recent', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "postListRecentRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/category/list', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "categoryListRequestHandler"],
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
        $post = $postsClass->getPostByPostType($postId, $postType);
        if (is_wp_error($post)) {
            return $this->controllerHelpers->sendErrorResponse(
                'post_fetch_error',
                "Post fetch error",
                $this->apiPostResponse
            );
        }
        $pageObject = Tru_Fetcher_Posts::buildPostObject($post);
        $this->apiPostResponse->setPost($pageObject);
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
        $postTemplate = $postsClass->getPostTemplateByPost($post);
        if (is_wp_error($postTemplate)) {
            return $this->sendResponse(
                "Post template fetch error",
                $this->apiPostResponse
            );
        }
        $this->apiPostResponse->setPost($post);
        $this->apiPostResponse->setPostTemplate($postTemplate);
        return $this->sendResponse(
            "Post fetch successful",
            $this->apiPostResponse
        );
    }
    public function postListRecentRequestHandler(\WP_REST_Request $request)
    {
        $postCount = 5;
        if (isset($request["number"])) {
            $postCount = (int) $request["number"];
        }
        $args = [
            'post_type' => "post",
            "orderby" => "date",
            "order" => "desc",
            "posts_per_page" => $postCount
        ];
        $postList = [];
        foreach (get_posts($args) as $post) {
            $categoryName = false;
            $category = get_field("post_template_category", $post->ID);
            if ($category) {
                $categoryName = $category->slug;
            }
            array_push($postList, [
                "name" => $post->post_title,
                "slug" => $post->post_name,
                "date" => $post->post_date_gmt,
                "thumb" => get_the_post_thumbnail_url($post->ID),
                "category" => $categoryName
            ]);
        }
        return $this->sendResponse(
            "Post fetch successful",
            $postList
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
        $showAllCategories = true;
        $categories = [];

        $paginationRequestData = $this->postHelpers->getPaginationRequestData($request);
        if (is_wp_error($paginationRequestData)) {
            return $this->controllerHelpers->sendErrorResponse(
                $paginationRequestData->get_error_code(),
                $paginationRequestData->get_error_message(),
                $this->postListResponse
            );
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
        $this->postListResponse->setPagination($pagination);
        $this->postListResponse->setPostList($this->postHelpers->buildPostsArray($postQuery->posts));
        return $this->controllerHelpers->sendSuccessResponse(
            "Post list request success",
            $this->postListResponse
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
