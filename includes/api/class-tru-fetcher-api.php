<?php

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
class Tru_Fetcher_Api {

	public function __construct() {
		$this->loadDependencies();
	}

	public function loadDependencies() {
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/auth/class-tru-fetcher-api-auth-jwt.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/controllers/class-tru-fetcher-api-page-controller.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'api/controllers/class-tru-fetcher-api-user-controller.php';
	}

	public function init() {
		$this->loadApiAuth();
		$this->loadApiControllers();
	}

	private function loadApiAuth() {
		$truFetcherAuth = new Tru_Fetcher_Api_Auth_Jwt();
		$truFetcherAuth->init();
	}

	public function loadApiControllers() {
		$pageController = new Tru_Fetcher_Api_Page_Controller();
		$pageController->init();
		$userController = new Tru_Fetcher_Api_User_Controller();
		$userController->init();
	}
}
