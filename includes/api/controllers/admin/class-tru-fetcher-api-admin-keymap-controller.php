<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Base_Controller;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Keymap_Response;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Tab_Preset_Response;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Keymaps;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Tab_Presets;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Setting;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Settings_Response;

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
class Tru_Fetcher_Api_Admin_Keymap_Controller extends Tru_Fetcher_Api_Admin_Base_Controller {

    private Tru_Fetcher_Api_Admin_Keymap_Response $keymapResponse;
    private Tru_Fetcher_Api_Helpers_Keymaps $keymapHelpers;

	public function __construct() {
        parent::__construct();
        $this->keymapHelpers = new Tru_Fetcher_Api_Helpers_Keymaps();
        $this->keymapResponse = new Tru_Fetcher_Api_Admin_Keymap_Response();
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	public function register_routes() {
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/keymap/keys/post', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchPostKeys" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/keymap/service/(?<service_id>[\d]+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchKeymap" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/keymap/service/(?<service_id>[\d]+)/save', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "saveKeymap" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );

		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/keymap/(?<id>[\d]+)/delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [ $this, "deleteKeymap" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
	}

    public function fetchPostKeys(\WP_REST_Request $request) {
        $this->keymapResponse->setKeys($this->keymapHelpers->getPostKeys());
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched Post keys",
            $this->keymapResponse
        );
    }
    public function fetchKeymap(\WP_REST_Request $request) {
        $serviceId = $request->get_param('service_id');
        if (empty($serviceId)) {
            return $this->controllerHelpers->sendErrorResponse(
                "keymap_error",
                "No service id provided",
                $this->keymapResponse
            );
        }
        $keymap = $this->keymapHelpers->getKeymap($serviceId);
        if (!$keymap) {
            return $this->controllerHelpers->sendErrorResponse(
                "keymap_error",
                "No keymap found",
                $this->keymapResponse
            );
        }
        $this->keymapResponse->setServiceId($serviceId);
        $this->keymapResponse->setKeymaps($keymap);
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched settings",
            $this->keymapResponse
        );
    }

    public function saveKeymap(\WP_REST_Request $request) {
        if (!$this->keymapHelpers->saveKeymapFromRequest($request)) {
            $this->keymapResponse->setErrors($this->keymapHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "keymap_error",
                "Failed to update keymap",
                $this->keymapResponse
            );
        }
        return $this->fetchKeymap($request);
    }

    public function deleteKeymap(\WP_REST_Request $request) {
        if (!$this->keymapHelpers->deleteKeymap($request)) {
            $this->keymapResponse->setErrors($this->keymapHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "keymap_error",
                "Failed to delete keymap",
                $this->keymapResponse
            );
        }
        return $this->controllerHelpers->sendSuccessResponse(
            "Deleted keymap",
            $this->keymapResponse
        );
    }
}
