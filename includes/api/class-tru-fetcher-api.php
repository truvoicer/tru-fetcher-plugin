<?php
namespace TruFetcher\Includes\Api;

use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Form_Preset_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Keymap_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Listings_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Posts_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_Settings_Controller;
use TruFetcher\Includes\Api\Controllers\Admin\Tru_Fetcher_Api_Admin_System_Controller;
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
use TruFetcher\Includes\Tru_Fetcher_Filters;

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
    const PUBLIC_CONTROLLERS = [
        Tru_Fetcher_Api_Auth_Controller::class,
        Tru_Fetcher_Api_Settings_Controller::class,
        Tru_Fetcher_Api_Page_Controller::class,
        Tru_Fetcher_Api_Posts_Controller::class,
        Tru_Fetcher_Api_User_Controller::class,
        Tru_Fetcher_Api_Comments_Controller::class,
        Tru_Fetcher_Api_Forms_Controller::class,
        Tru_Fetcher_Api_General_Controller::class,
        Tru_Fetcher_Api_List_Controller::class,
    ];

    const ADMIN_CONTROLLERS = [
        Tru_Fetcher_Api_Admin_Token_Controller::class,
        Tru_Fetcher_Api_Admin_Posts_Controller::class,
        Tru_Fetcher_Api_Admin_Settings_Controller::class,
        Tru_Fetcher_Api_Admin_Form_Preset_Controller::class,
        Tru_Fetcher_Api_Admin_Tab_Preset_Controller::class,
        Tru_Fetcher_Api_Admin_Keymap_Controller::class,
        Tru_Fetcher_Api_Admin_Listings_Controller::class,
        Tru_Fetcher_Api_Admin_System_Controller::class,
    ];

	public function init() {
		$this->loadPublicApiControllers();
		$this->loadAdminApiControllers();
	}


	public function loadPublicApiControllers() {
        $controllers = self::PUBLIC_CONTROLLERS;
        $applyFilters = apply_filters(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_API_PUBLIC_CONTROLLERS, []);
        if (is_array($applyFilters)) {
            $controllers = array_merge(self::PUBLIC_CONTROLLERS, $applyFilters);
        }
        foreach ($controllers as $controller) {
            (new $controller())->init();
        }
	}

	public function loadAdminApiControllers() {
        $controllers = self::ADMIN_CONTROLLERS;
        $applyFilters = apply_filters(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_API_PUBLIC_CONTROLLERS, []);
        if (is_array($applyFilters)) {
            $controllers = array_merge(self::ADMIN_CONTROLLERS, $applyFilters);
        }
        foreach ($controllers as $controller) {
            (new $controller())->init();
        }
	}
}
