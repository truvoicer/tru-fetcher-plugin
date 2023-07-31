<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Base_Controller;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Form_Preset_Response;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Form_Presets;
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
class Tru_Fetcher_Api_Admin_Form_Preset_Controller extends Tru_Fetcher_Api_Admin_Base_Controller {

    private Tru_Fetcher_Api_Admin_Form_Preset_Response $formPresetResponse;
    private Tru_Fetcher_Api_Helpers_Form_Presets $formPresetHelpers;

	public function __construct() {
        parent::__construct();
        $this->formPresetHelpers = new Tru_Fetcher_Api_Helpers_Form_Presets();
        $this->formPresetResponse = new Tru_Fetcher_Api_Admin_Form_Preset_Response();
	}

	public function init() {
		add_action( 'rest_api_init', [ $this, "register_routes" ] );
	}

	public function register_routes() {
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/form/presets', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchFormPresets" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/form/presets/(?<name>[\w_]+)', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchSingleFormPreset" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/form/presets/create', array(
			'methods'             => \WP_REST_Server::CREATABLE,
			'callback'            => [ $this, "createFormPreset" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/form/presets/(?<id>[\d]+)/update', array(
			'methods'             => \WP_REST_Server::EDITABLE,
			'callback'            => [ $this, "updateFormPreset" ],
            'permission_callback' => [$this->apiAuth, 'allowRequest'],
		) );
		register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/form/presets/delete', array(
			'methods'             => \WP_REST_Server::DELETABLE,
			'callback'            => [ $this, "deleteFormPreset" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
		) );
	}

    public function fetchFormPresets(\WP_REST_Request $request) {
        $this->formPresetResponse->setFormPreset($this->formPresetHelpers->getFormPresetsRepository()->findFormPresets());
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched form presets",
            $this->formPresetResponse
        );
    }
    public function fetchSingleFormPreset(\WP_REST_Request $request) {
        $name = $request->get_param('name');
        if (empty($name)) {
            return $this->controllerHelpers->sendErrorResponse(
                "form_preset_error",
                "No name provided",
                $this->formPresetResponse
            );
        }
        $formPreset = $this->formPresetHelpers->getFormPreset($name);
        if (!$formPreset) {
            return $this->controllerHelpers->sendErrorResponse(
                "form_preset_error",
                "No form preset found",
                $this->formPresetResponse
            );
        }
        $this->formPresetResponse->setFormPreset($formPreset);
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched settings",
            $this->formPresetResponse
        );
    }

    public function createFormPreset(\WP_REST_Request $request) {
        if (!$this->formPresetHelpers->createFormPresetFromRequest($request)) {
            $this->formPresetResponse->setErrors($this->formPresetHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "form_preset_error",
                "Failed to create form preset",
                $this->formPresetResponse
            );
        }
        return $this->fetchFormPresets($request);
    }

    public function updateFormPreset(\WP_REST_Request $request) {
        if (!$this->formPresetHelpers->updateFormPresetFromRequest($request)) {
            $this->formPresetResponse->setErrors($this->formPresetHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "form_preset_error",
                "Failed to update form preset",
                $this->formPresetResponse
            );
        }
        return $this->fetchFormPresets($request);
    }

    public function deleteFormPreset(\WP_REST_Request $request) {
        if (!$this->formPresetHelpers->deleteFormPreset($request)) {
            $this->formPresetResponse->setErrors($this->formPresetHelpers->getErrors());
            return $this->controllerHelpers->sendErrorResponse(
                "form_preset_error",
                "Failed to delete form preset",
                $this->formPresetResponse
            );
        }
        return $this->fetchFormPresets($request);
    }
}
