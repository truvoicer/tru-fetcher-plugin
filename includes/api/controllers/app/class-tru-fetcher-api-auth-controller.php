<?php

namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Auth_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Token_Response;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Api_Tokens;

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
class Tru_Fetcher_Api_Auth_Controller extends Tru_Fetcher_Api_Controller_Base
{
    protected Tru_Fetcher_Api_Token_Response $tokenResponse;
    private Tru_Fetcher_DB_Repository_Api_Tokens $apiTokensRepository;

    public function __construct()
    {
        parent::__construct();
        $this->apiTokensRepository = new Tru_Fetcher_DB_Repository_Api_Tokens();
        $this->apiConfigEndpoints->endpointsInit('/auth');
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
        $this->tokenResponse = new Tru_Fetcher_Api_Token_Response();
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/register', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "authRegister"],
            'permission_callback' => [$this->apiAuthApp, 'publicTokenRequestHandler']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/login', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "authLogin"],
            'permission_callback' => [$this->apiAuthApp, 'publicTokenRequestHandler']
        ));
        register_rest_route($this->apiConfigEndpoints->publicEndpoint, '/token/check', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "authTokenCheck"],
            'permission_callback' => [$this->apiAuthApp, 'publicTokenRequestHandler']
        ));
        register_rest_route($this->apiConfigEndpoints->protectedEndpoint, '/token/check', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "authTokenCheck"],
            'permission_callback' => [$this->apiAuthApp, 'protectedTokenRequestHandler']
        ));
    }

    public function authLogin(\WP_REST_Request $request) {
        $handleLogin = $this->apiAuthApp->loginRequestHandler($request);
        if (is_wp_error($handleLogin)) {
            return $this->controllerHelpers->sendWpErrorResponse($handleLogin, $this->tokenResponse);
        }

        if ($this->apiAuthApp->hasErrors()) {
            $this->tokenResponse->setErrors($this->apiAuthApp->getErrors());
        }

        $apiModel = $this->apiTokensRepository->getModel();

        $issuedAt = $handleLogin[$apiModel->getIssuedAtColumn()];
        $expiresAt = $handleLogin[$apiModel->getExpiresAtColumn()];

        $this->tokenResponse->setToken($handleLogin[$apiModel->getTokenColumn()]);
        $this->tokenResponse->setIssuedAt(strtotime($issuedAt));
        $this->tokenResponse->setExpiresAt(strtotime($expiresAt));

        $this->tokenResponse->setData($this->apiAuthApp->buildUserTokenResponseData());
        return $this->controllerHelpers->sendSuccessResponse(
            'Login success',
            $this->tokenResponse
        );
    }

    public function authRegister(\WP_REST_Request $request) {
        $handleRegister = $this->apiAuthApp->registerRequestHandler($request);
        if (is_wp_error($handleRegister)) {
            return $this->controllerHelpers->sendWpErrorResponse($handleRegister, $this->tokenResponse);
        }

        $apiModel = $this->apiTokensRepository->getModel();

        $issuedAt = $handleRegister[$apiModel->getIssuedAtColumn()];
        $expiresAt = $handleRegister[$apiModel->getExpiresAtColumn()];

        $this->tokenResponse->setToken($handleRegister[$apiModel->getTokenColumn()]);
        $this->tokenResponse->setIssuedAt(strtotime($issuedAt));
        $this->tokenResponse->setExpiresAt(strtotime($expiresAt));
//        var_dump($this->apiAuthApp->getUser());
        $this->tokenResponse->setData($this->apiAuthApp->buildUserTokenResponseData());
        return $this->controllerHelpers->sendSuccessResponse(
            'Register success',
            $this->tokenResponse
        );
    }

    public function authTokenCheck(\WP_REST_Request $request) {
        $this->tokenResponse->setData($this->apiAuthApp->buildUserTokenResponseData());
        return $this->controllerHelpers->sendSuccessResponse(
            'Validate success',
            $this->tokenResponse
        );
    }
}
