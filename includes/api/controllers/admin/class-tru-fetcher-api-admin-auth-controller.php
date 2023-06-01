<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Auth_Response;

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
class Tru_Fetcher_Api_Admin_Auth_Controller extends Tru_Fetcher_Api_Admin_Base_Controller
{
    private Tru_Fetcher_Api_Auth_Response $authResponse;

    public function __construct()
    {
        parent::__construct();
        $this->authResponse = new Tru_Fetcher_Api_Auth_Response();
    }

    public function init()
    {
        add_action('rest_api_init', [$this, "register_routes"]);
    }

    public function register_routes()
    {
        register_rest_route($this->adminNamespace, '/auth/roles', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "fetchRoles"],
            'permission_callback' => [$this->apiAuth, 'allowRequest']
        ));
    }

    public function fetchRoles(\WP_REST_Request $request)
    {
        $this->authResponse->setRoles($this->apiAuth->fetchRoles());
        return $this->controllerHelpers->sendSuccessResponse(
            "Token is valid",
            $this->authResponse
        );
    }

}
