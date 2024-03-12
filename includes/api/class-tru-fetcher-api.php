<?php
namespace TruFetcher\Includes\Api;

use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Form_Preset_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Posts_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Settings_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Tab_Preset_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Token_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_Auth_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_Comments_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_Forms_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_General_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_List_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_Page_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_Posts_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_Settings_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_User_Controller;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_User_Profile_Controller;

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

	public function init() {
		$this->loadPublicApiControllers();
		$this->loadAdminApiControllers();
	}


	public function loadPublicApiControllers() {
        (new Tru_Fetcher_Api_Auth_Controller())->init();
        (new Tru_Fetcher_Api_Settings_Controller())->init();
        (new Tru_Fetcher_Api_Page_Controller())->init();
        (new Tru_Fetcher_Api_Posts_Controller())->init();
        (new Tru_Fetcher_Api_User_Controller())->init();
        (new Tru_Fetcher_Api_Comments_Controller())->init();
        (new Tru_Fetcher_Api_Forms_Controller())->init();
        (new Tru_Fetcher_Api_General_Controller())->init();
        (new Tru_Fetcher_Api_User_Profile_Controller())->init();
        (new Tru_Fetcher_Api_List_Controller())->init();
	}

	public function loadAdminApiControllers() {
		(new Tru_Fetcher_Api_Admin_Token_Controller())->init();
		(new Tru_Fetcher_Api_Admin_Posts_Controller())->init();
		(new Tru_Fetcher_Api_Admin_Settings_Controller())->init();
		(new Tru_Fetcher_Api_Admin_Form_Preset_Controller())->init();
		(new Tru_Fetcher_Api_Admin_Tab_Preset_Controller())->init();
	}
}
