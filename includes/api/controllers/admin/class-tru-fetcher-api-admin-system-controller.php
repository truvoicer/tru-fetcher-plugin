<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;

use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_System_Response;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

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
class Tru_Fetcher_Api_Admin_System_Controller extends Tru_Fetcher_Api_Admin_Base_Controller
{

    private Tru_Fetcher_Api_Admin_System_Response $apiSystemResponse;

    public function __construct()
    {
        parent::__construct();
    }

    public function init()
    {
        $this->load_dependencies();
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }

    private function load_dependencies()
    {
    }

    private function loadResponseObjects()
    {
        $this->apiSystemResponse = new Tru_Fetcher_Api_Admin_System_Response();
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->adminNamespace, '/system/names', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "fetchSystemNames"],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ));
    }

    public function fetchSystemNames()
    {

        $this->apiSystemResponse->setData([
            \WP_User::class => [
                'nickname',
                'description',
                'user_description',
                'first_name',
                'user_firstname',
                'last_name',
                'user_lastname',
                'user_login',
                'user_pass',
                'user_nicename',
                'user_email',
                'user_url',
                'user_registered',
                'user_activation_key',
                'user_status',
                'user_level',
                'display_name',
                'spam',
                'deleted',
                'locale',
                'rich_editing',
                'syntax_highlighting',
                'use_ssl',
            ],
            ...Tru_Fetcher_Helpers::getClassProperties([
                \WP_Post::class,
            ])
        ]);

        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched",
            $this->apiSystemResponse
        );
    }
}
