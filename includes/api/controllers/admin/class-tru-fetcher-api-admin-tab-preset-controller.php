<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Base_Controller;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Tab_Preset_Response;
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
class Tru_Fetcher_Api_Admin_Tab_Preset_Controller extends Tru_Fetcher_Api_Admin_Base_Controller {

    private Tru_Fetcher_Api_Admin_Tab_Preset_Response $tabPresetResponse;
    private Tru_Fetcher_Api_Helpers_Tab_Presets $tabPresetHelpers;

	public function __construct() {
        parent::__construct();
        $this->tabPresetHelpers = new Tru_Fetcher_Api_Helpers_Tab_Presets();
        $this->tabPresetResponse = new Tru_Fetcher_Api_Admin_Tab_Preset_Response();
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	public function register_routes() {
        register_rest_route( $this->adminNamespace, '/tab/presets', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchTabPresets" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->adminNamespace, '/tab/presets/(?<name>[\w_]+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchSingleTabPreset" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
		register_rest_route( $this->adminNamespace, '/tab/presets/create', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createTabPreset" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->adminNamespace, '/tab/presets/(?<id>[\d]+)/update', array(
			'methods'             => \WP_REST_Server::EDITABLE,
			'callback'            => [ $this, "updateTabPreset" ],
            'permission_callback' => [$this->apiAuth, 'allowRequest'],
		) );
		register_rest_route( $this->adminNamespace, '/tab/presets/delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [ $this, "deleteTabPreset" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
	}

    public function fetchTabPresets(\WP_REST_Request $request) {
        $buildConfigData = $request->get_param('build_config_data');

        $this->tabPresetResponse->setTabPreset(
            $this->tabPresetHelpers
                ->getTabPresetsRepository()
                ->findTabPresets(
                    (bool)$buildConfigData
                )
        );
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched tab presets",
            $this->tabPresetResponse
        );
    }
    public function fetchSingleTabPreset(\WP_REST_Request $request) {
        $name = $request->get_param('name');
        if (empty($name)) {
            return $this->controllerHelpers->sendErrorResponse(
                "tab_preset_error",
                "No name provided",
                $this->tabPresetResponse
            );
        }
        $tabPreset = $this->tabPresetHelpers->getTabPreset($name);
        if (!$tabPreset) {
            return $this->controllerHelpers->sendErrorResponse(
                "tab_preset_error",
                "No tab preset found",
                $this->tabPresetResponse
            );
        }
        $this->tabPresetResponse->setTabPreset($tabPreset);
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched settings",
            $this->tabPresetResponse
        );
    }

    public function createTabPreset(\WP_REST_Request $request) {
        if (!$this->tabPresetHelpers->createTabPresetFromRequest($request)) {
            $this->tabPresetResponse->setErrors($this->tabPresetHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "tab_preset_error",
                "Failed to create tab preset",
                $this->tabPresetResponse
            );
        }
        return $this->fetchTabPresets($request);
    }

    public function updateTabPreset(\WP_REST_Request $request) {
        if (!$this->tabPresetHelpers->updateTabPresetFromRequest($request)) {
            $this->tabPresetResponse->setErrors($this->tabPresetHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "tab_preset_error",
                "Failed to update tab preset",
                $this->tabPresetResponse
            );
        }
        return $this->fetchTabPresets($request);
    }

    public function deleteTabPreset(\WP_REST_Request $request) {
        if (!$this->tabPresetHelpers->deleteTabPreset($request)) {
            $this->tabPresetResponse->setErrors($this->tabPresetHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "tab_preset_error",
                "Failed to delete tab preset",
                $this->tabPresetResponse
            );
        }
        return $this->fetchTabPresets($request);
    }
}
