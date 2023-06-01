<?php

namespace TruFetcher\Includes\Api\Controllers\App\Protected;

use TruFetcher\Includes\Admin\Taxonomies\Tru_Fetcher_Categories_Taxonomy;
use TruFetcher\Includes\Api\Controllers\App\Tru_Fetcher_Api_Controller_Base;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Posts_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_User_Response;
use TruFetcher\Includes\Posts\Tru_Fetcher_Posts;

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
class Tru_Fetcher_Api_App_Account_Controller extends Tru_Fetcher_Api_Controller_Base
{

    private string $publicEndpoint;
    private string $protectedEndpoint;
    private Tru_Fetcher_Api_User_Response $apiUserResponse;
    private Tru_Fetcher_Posts $postsManager;

    public function __construct()
    {
        parent::__construct();
        $this->publicEndpoint = $this->publicNamespace;
        $this->protectedEndpoint = $this->protectedNamespace;
    }

    public function init()
    {
        $this->load_dependencies();
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }

    private function load_dependencies()
    {
        $this->postsManager = new Tru_Fetcher_Posts();
    }

    private function loadResponseObjects()
    {
        $this->apiUserResponse = new Tru_Fetcher_Api_User_Response();
    }

    public function register_routes()
    {
        register_rest_route($this->protectedEndpoint, '/account/details', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "fetchUserAccountDetails"],
            'permission_callback' => [$this->apiAuthApp, "protectedTokenRequestHandler"],
        ));
    }

    public function fetchUserAccountDetails()
    {
        $this->apiUserResponse->setUser($this->apiAuthApp->getUser());
        return $this->controllerHelpers->sendSuccessResponse(
            "Posts fetch success",
            $this->apiUserResponse
        );
    }

}
