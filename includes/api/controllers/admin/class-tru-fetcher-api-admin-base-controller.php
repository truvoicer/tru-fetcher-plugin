<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;

use TruFetcher\Includes\Api\Config\Tru_Fetcher_Api_Config_Endpoints;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Controller;
use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth_Admin;

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
class Tru_Fetcher_Api_Admin_Base_Controller {

    protected Tru_Fetcher_Api_Auth_Admin $apiAuth;

    protected Tru_Fetcher_Api_Helpers_Controller $controllerHelpers;
    protected Tru_Fetcher_Api_Config_Endpoints $apiConfigEndpoints;

    public function __construct()
    {
        $this->controllerHelpers = new Tru_Fetcher_Api_Helpers_Controller();
        $this->apiAuth = new Tru_Fetcher_Api_Auth_Admin();
        $this->apiConfigEndpoints = new Tru_Fetcher_Api_Config_Endpoints();
    }

}
