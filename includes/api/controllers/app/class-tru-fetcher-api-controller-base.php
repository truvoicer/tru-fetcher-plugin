<?php

namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Config\Tru_Fetcher_Api_Config_Endpoints;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Controller;
use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth_App;

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
class Tru_Fetcher_Api_Controller_Base {
    const STATUS_SUCCESS = "success";

    private \WP_User $user;

    protected Tru_Fetcher_Api_Helpers_Controller $controllerHelpers;

    protected Tru_Fetcher_Api_Auth_App $apiAuthApp;
    protected Tru_Fetcher_Api_Config_Endpoints $apiConfigEndpoints;

    public function __construct()
    {
        $this->controllerHelpers = new Tru_Fetcher_Api_Helpers_Controller();
        $this->apiAuthApp = new Tru_Fetcher_Api_Auth_App();
        $this->apiConfigEndpoints = new Tru_Fetcher_Api_Config_Endpoints();
    }

    protected function showError( $code, $message ) {
        return new \WP_Error( $code,
            esc_html__( $message, 'my-text-domain' ),
            array( 'status' => 404 ) );
    }


    protected function isNotEmpty($item) {
        return (isset($item) && $item !== "");
    }

    /**
     * @return \WP_User
     */
    public function getUser(): \WP_User
    {
        return $this->user;
    }

    /**
     * @param \WP_User $user
     */
    public function setUser(\WP_User $user): void
    {
        $this->user = $user;
    }

}
