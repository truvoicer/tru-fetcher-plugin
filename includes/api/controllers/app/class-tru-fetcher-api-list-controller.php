<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_List_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_List_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Post_Response;
use TruFetcher\Includes\Constants\Tru_Fetcher_Constants_Api;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Keymaps;
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
        $displayAs = $request->get_param('display_as');
        if (empty($id)) {
            return $this->controllerHelpers->sendErrorResponse(
                'invalid_request',
                "Invalid request, id is required",
                $this->listResponse
            );
        }

        $id = (int)$id;

        $paginationRequestData = $this->postHelpers->getPaginationRequestData($request);
        if (is_wp_error($paginationRequestData)) {
            return $this->controllerHelpers->sendErrorResponse(
                $paginationRequestData->get_error_code(),
                $paginationRequestData->get_error_message(),
                $this->listResponse
            );
        }

        $post = $this->postHelpers->getPostTypePostById(
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
//        private Tru_Fetcher_Api_Helpers_Keymaps $keymapHelpers;
//        $this->keymapHelpers = new Tru_Fetcher_Api_Helpers_Keymaps();
//        $keymap = $this->keymapHelpers->getKeymap((int)$data[self::SERVICE_ID]);
//        $dataKeys = $data[Tru_Fetcher_Admin_Meta_Box_Single_Item::DATA_KEYS_ID];
//        $buildDataKeys = $this->keymapHelpers->mapDataKeysWithKeymap(
//            $dataKeys,
//            $keymap
//        );
//        var_dump($buildDataKeys); die;
        $buildItemList = (new Tru_Fetcher_Post_Types_Trf_Item_List())
            ->setDisplayAs($displayAs)
            ->renderPost($post);
        $postsPerPage = $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['POST_PER_PAGE']];
        $pageNumber = $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['PAGE_NUMBER']];
        $pagination = Tru_Fetcher_Posts::getPagination(
            count($buildItemList),
            $paginationRequestData[Tru_Fetcher_Constants_Api::REQUEST_KEYS['OFFSET']],
            $postsPerPage,
            $pageNumber
        );

        $sliceList = array_slice(
            $buildItemList,
            Tru_Fetcher_Posts::calculateOffset($pageNumber, $postsPerPage),
            $postsPerPage
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
