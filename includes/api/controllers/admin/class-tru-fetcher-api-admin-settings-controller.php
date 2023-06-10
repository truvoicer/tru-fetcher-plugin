<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Base_Controller;
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
class Tru_Fetcher_Api_Admin_Settings_Controller extends Tru_Fetcher_Api_Admin_Base_Controller {

    private Tru_Fetcher_Api_Admin_Settings_Response $apiSettingsResponse;
    private Tru_Fetcher_Api_Helpers_Setting $settings;

	public function __construct() {
        parent::__construct();
        $this->settings = new Tru_Fetcher_Api_Helpers_Setting();
	}

	public function init() {
		$this->load_dependencies();
		$this->loadResponseObjects();
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	private function load_dependencies() {}

	private function loadResponseObjects() {
        $this->apiSettingsResponse = new Tru_Fetcher_Api_Admin_Settings_Response();
	}

	public function register_routes() {
        register_rest_route( $this->adminNamespace, '/settings', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchSettings" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->adminNamespace, '/settings/(?<name>[\w_]+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchSingleSetting" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
		register_rest_route( $this->adminNamespace, '/settings/create', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createSetting" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->adminNamespace, '/settings/(?<id>[\d]+)/update', array(
			'methods'             => \WP_REST_Server::EDITABLE,
			'callback'            => [ $this, "updateSetting" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->adminNamespace, '/settings/delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [ $this, "deleteSetting" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
	}

    public function fetchSingleSetting(\WP_REST_Request $request) {
        $settingsRepo = $this->settings->getSettingsRepository();
        $name = $request->get_param('name');
        if (!isset($name)) {
            return $this->controllerHelpers->sendErrorResponse(
                "request_error",
                "Setting name is invalid",
                $this->apiSettingsResponse
            );
        }
        $fetch = $settingsRepo->findSettingByName($name);
        if ($settingsRepo->hasErrors()) {
            $this->apiSettingsResponse->setErrors($settingsRepo->getErrors());
        }
        if ($fetch) {
            $this->apiSettingsResponse->setSettings($fetch);
        }
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched settings",
            $this->apiSettingsResponse
        );
    }
    public function fetchSettings(\WP_REST_Request $request) {
        $settingsRepo = $this->settings->getSettingsRepository();
        $fetch = $settingsRepo->findSettings();
        if ($settingsRepo->hasErrors()) {
            $this->apiSettingsResponse->setErrors($settingsRepo->getErrors());
        }
        if ($fetch) {
            $this->apiSettingsResponse->setSettings($fetch);
        }
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched settings",
            $this->apiSettingsResponse
        );
    }

    public function createSetting(\WP_REST_Request $request) {
        $create = $this->settings->createSingleSettingsFromRequest($request);
        return $this->fetchSettings($request);
    }

    public function updateSetting(\WP_REST_Request $request) {
        $update = $this->settings->updateSingleSettingsFromRequest($request);
        return $this->fetchSettings($request);
    }

    public function deleteSetting(\WP_REST_Request $request) {
        $this->settings->deleteSettings($request);
        return $this->fetchSettings($request);
    }
}
