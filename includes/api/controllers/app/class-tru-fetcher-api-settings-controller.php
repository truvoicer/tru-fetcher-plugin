<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Page_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Settings_Response;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Setting;
use TruFetcher\Includes\Listings\Tru_Fetcher_Listings;
use TruFetcher\Includes\Menus\Tru_Fetcher_Menu;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;
use TruFetcher\Includes\Sidebars\Tru_Fetcher_Sidebars;

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
class Tru_Fetcher_Api_Settings_Controller extends Tru_Fetcher_Api_Controller_Base {

	private Tru_Fetcher_Api_Settings_Response $apiSettingsResponse;
    private Tru_Fetcher_Api_Helpers_Setting $settingHelpers;

    public function __construct() {
        parent::__construct();
        $this->apiConfigEndpoints->endpointsInit('/settings');
        $this->settingHelpers = new Tru_Fetcher_Api_Helpers_Setting();
    }

	public function init() {
        $this->apiSettingsResponse = new Tru_Fetcher_Api_Settings_Response();
		add_action( 'rest_api_init', [$this, "register_routes"] );
	}


	public function register_routes() {
		register_rest_route( $this->apiConfigEndpoints->publicNamespace, $this->apiConfigEndpoints->namespace, array(
			'methods'  => \WP_REST_Server::READABLE,
			'callback' => [ $this, "getSiteSettings"],
			'permission_callback' => [$this->apiAuthApp, 'allowRequest']
		) );
	}

	public function getSiteSettings(\WP_REST_Request $request) {
        $this->apiSettingsResponse->setSettings($this->getSiteConfig());
        return $this->controllerHelpers->sendSuccessResponse(
            'Page template fetch',
            $this->apiSettingsResponse
        );
	}

	private function getSiteConfig() {
        return array_merge(
            $this->settingHelpers->getFormattedSettings(['api_key', 'api_url', 'docker_api_url', 'docker_api_key', 'docker']),
            $this->settingHelpers->getWordpressSettings()
        );

	}
}
