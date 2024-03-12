<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_List_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_List_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_Response;
use TruFetcher\Includes\Constants\Tru_Fetcher_Constants_Api;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_List;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Single_Item;
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
class Tru_Fetcher_Api_List_Controller extends Tru_Fetcher_Api_Controller_Base
{
    private Tru_Fetcher_Posts $postHelpers;
    private Tru_Fetcher_Api_List_Response $listResponse;

    public function __construct()
    {
        parent::__construct();
        $this->apiConfigEndpoints->endpointsInit('/list');
    }

    public function init()
    {
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }


    private function loadResponseObjects()
    {
        $this->listResponse = new Tru_Fetcher_Api_List_Response();
        $this->postHelpers = new Tru_Fetcher_Posts();
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/(?<id>[\d-]+)', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "itemListRequestHandler"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
    }

    public function itemListRequestHandler(\WP_REST_Request $request)
    {
        $id = $request->get_param('id');
        if (empty($id)) {
            return $this->controllerHelpers->sendErrorResponse(
                'invalid_request',
                "Invalid request, id is required",
                $this->listResponse
            );
        }
        $id = (int)$id;
        $showAllCategories = true;
        $categories = [];

        $paginationRequestData = $this->postHelpers->getPaginationRequestData($request);
        if (is_wp_error($paginationRequestData)) {
            return $this->controllerHelpers->sendErrorResponse(
                $paginationRequestData->get_error_code(),
                $paginationRequestData->get_error_message(),
                $this->listResponse
            );
        }

        $post = $this->postHelpers->getPostByPostType(
            $id,
            Tru_Fetcher_Post_Types_Trf_Item_List::NAME
        );
        if (is_wp_error($post)) {
            return $this->controllerHelpers->sendErrorResponse(
                $post->get_error_code(),
                $post->get_error_message(),
                $this->listResponse
            );
        }


//        if (isset($request["show_all_categories"])) {
//            $showAllCategories = $request["show_all_categories"];
//        }
//        if (isset($request["categories"])) {
//            $categories = $request["categories"];
//            if (is_array($categories) && count($categories) > 0) {
//                $categories = implode(",", $categories);
//            } else {
//                $categories = 0;
//            }
//        }
//        $args = [
//            'cat' => $showAllCategories ? 0 : $categories,
//            'orderby' => 'date',
//            'order' => 'DESC',
//            'post_type' => 'post',
//            'meta_key' => '_thumbnail_id',
//        ];
//
//        $offsetArgs = [
//            'posts_per_page' => $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']],
//            'offset' => $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']],
//        ];
//
//        $allPostsQuery = new \WP_Query($args);
//        $postQuery = new \WP_Query(array_merge($args, $offsetArgs));
//
        $buildItemList = (new Tru_Fetcher_Post_Types_Trf_Item_List())->renderPost($post);
        $sliceList = array_slice(
            $buildItemList,
            $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']],
            $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']]
        );
        $pagination = Tru_Fetcher_Posts::getPagination(
            count($buildItemList),
            $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']],
            $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']]
        );
        $this->listResponse->setList($sliceList);
        $pagination->setPaginationType($paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGINATION_TYPE']]);
        $pagination->setCurrentPerPage(count($sliceList));
        $this->listResponse->setPagination($pagination);
        return $this->controllerHelpers->sendSuccessResponse(
            "List request success",
            $this->listResponse
        );
    }

}
