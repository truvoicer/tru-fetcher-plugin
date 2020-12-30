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
	    Tru_Fetcher_Class_Loader::loadClassList([
            'includes/api/auth/class-tru-fetcher-api-auth-jwt.php',
            'includes/api/controllers/class-tru-fetcher-api-page-controller.php',
            'includes/api/controllers/class-tru-fetcher-api-posts-controller.php',
            'includes/api/controllers/class-tru-fetcher-api-user-controller.php',
            'includes/api/controllers/class-tru-fetcher-api-comments-controller.php',
            'includes/api/controllers/class-tru-fetcher-api-forms-controller.php'
        ]);
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
		$postsController = new Tru_Fetcher_Api_Posts_Controller();
		$postsController->init();
		$userController = new Tru_Fetcher_Api_User_Controller();
		$userController->init();
		$commentsController = new Tru_Fetcher_Api_Comments_Controller();
        $commentsController->init();
		$formsController = new Tru_Fetcher_Api_Forms_Controller();
        $formsController->init();
	}
}
