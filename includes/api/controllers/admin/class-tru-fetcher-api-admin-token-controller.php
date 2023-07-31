<?php

namespace TruFetcher\Includes\Api\Controllers\Admin;

use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Token_Response;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Token_Response;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Api_Tokens;
use TruFetcher\Includes\User\Tru_Fetcher_User;

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
class Tru_Fetcher_Api_Admin_Token_Controller extends Tru_Fetcher_Api_Admin_Base_Controller
{
    private Tru_Fetcher_Api_Admin_Token_Response $adminTokenResponse;
    private Tru_Fetcher_Api_Token_Response $tokenResponse;

    private Tru_Fetcher_DB_Repository_Api_Tokens $apiTokensRepository;

    public function __construct()
    {
        parent::__construct();
        $this->apiTokensRepository = new Tru_Fetcher_DB_Repository_Api_Tokens();
    }

    public function init()
    {
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }


    private function loadResponseObjects()
    {
        $this->adminTokenResponse = new Tru_Fetcher_Api_Admin_Token_Response();
        $this->tokenResponse = new Tru_Fetcher_Api_Token_Response();
    }

    public function register_routes()
    {
        register_rest_route($this->apiConfigEndpoints->adminNamespace, '/token/check', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "tokenCheckHandler"],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler']
        ));
        register_rest_route($this->apiConfigEndpoints->adminNamespace, '/token/refresh', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "tokenRefreshHandler"],
            'permission_callback' => [$this->apiAuth, 'nonceRequestHandler']
        ));
        register_rest_route($this->apiConfigEndpoints->adminNamespace, '/token/generate', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "tokenGenerateHandler"],
            'permission_callback' => [$this->apiAuth, 'nonceRequestHandler']
        ));
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/tokens', array(
            'methods'             => \WP_REST_Server::READABLE,
            'callback'            => [ $this, "fetchTokens" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/token/create', array(
            'methods'             => \WP_REST_Server::CREATABLE,
            'callback'            => [ $this, "createToken" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/token/(?<id>[\d]+)/update', array(
            'methods'             => \WP_REST_Server::EDITABLE,
            'callback'            => [ $this, "updateToken" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
        register_rest_route( $this->apiConfigEndpoints->adminNamespace, '/token/delete', array(
            'methods'             => \WP_REST_Server::DELETABLE,
            'callback'            => [ $this, "deleteToken" ],
            'permission_callback' => [$this->apiAuth, 'tokenRequestHandler'],
        ) );
    }

    public function tokenGenerateHandler(\WP_REST_Request $request)
    {
        return $this->generateTokenRequest($request);
    }

    public function tokenCheckHandler(\WP_REST_Request $request)
    {
        return $this->controllerHelpers->sendSuccessResponse(
            "Token is valid",
            $this->tokenResponse
        );
    }

    public function tokenRefreshHandler(\WP_REST_Request $request)
    {
        return $this->generateTokenRequest($request);
    }

    public function generateTokenRequest(\WP_REST_Request $request) {
        $appKey = $request->get_param('app_key');
        if (!$appKey) {
            $this->tokenResponse->addError(
                new \WP_Error(
                    $this->tokenResponse::BASE_API_RESPONSE_ERROR_CODE_PREFIX . "app_key",
                    "App key is required"
                )
            );
            return $this->controllerHelpers->sendErrorResponse(
                $this->tokenResponse::BASE_API_RESPONSE_ERROR_CODE_PREFIX,
                "Token error",
                $this->tokenResponse
            );
        }
        $generateToken = $this->apiAuth->generateToken($appKey);
        if (is_wp_error($generateToken)) {
            $this->tokenResponse->addError($generateToken);
            return $this->controllerHelpers->sendErrorResponse(
                $this->tokenResponse::BASE_API_RESPONSE_ERROR_CODE_PREFIX,
                "Token error",
                $this->tokenResponse
            );
        }

        $model = $this->apiTokensRepository->getModel();
        $issuedAt = $generateToken[$model->getIssuedAtColumn()];
        $expiresAt = $generateToken[$model->getExpiresAtColumn()];

        $this->tokenResponse->setToken($generateToken[$model->getTokenColumn()]);
        $this->tokenResponse->setIssuedAt(strtotime($issuedAt));
        $this->tokenResponse->setExpiresAt(strtotime($expiresAt));
        return $this->controllerHelpers->sendSuccessResponse(
            "Token successfully refreshed",
            $this->tokenResponse
        );
    }

    public function fetchTokens(\WP_REST_Request $request) {
        $apiTokensRepo = $this->apiAuth->getApiTokensRepository();
        $fetch = $apiTokensRepo->findMany();
        if ($apiTokensRepo->hasErrors()) {
            $this->adminTokenResponse->setErrors($apiTokensRepo->getErrors());
        }
        if (is_array($fetch)) {
            $this->adminTokenResponse->setTokens($fetch);
        }
        return $this->controllerHelpers->sendSuccessResponse(
            "Fetched option groups",
            $this->adminTokenResponse
        );
    }
    public function createToken(\WP_REST_Request $request) {
        $userId = $request->get_param('user_id');
        if (!$userId) {
            $this->adminTokenResponse->addError(
                new \WP_Error(
                    $this->adminTokenResponse::API_RESPONSE_ERROR_CODE_PREFIX,
                    "User id is required"
                )
            );
            return $this->controllerHelpers->sendErrorResponse(
                $this->adminTokenResponse::API_RESPONSE_ERROR_CODE_PREFIX,
                "Create token error",
                $this->adminTokenResponse
            );
        }
        $user = Tru_Fetcher_User::getUserById($userId);
        if (is_wp_error($user)) {
            $this->adminTokenResponse->addError(
                new \WP_Error(
                    $user->get_error_code(),
                    $user->get_error_message()
                )
            );
            return $this->controllerHelpers->sendErrorResponse(
                $this->adminTokenResponse::API_RESPONSE_ERROR_CODE_PREFIX,
                "Error finding user",
                $this->adminTokenResponse
            );
        }
        $this->apiAuth->setUser($user);
        return $this->generateTokenRequest($request);
    }

    public function updateToken(\WP_REST_Request $request) {
        $update = $this->apiAuth->updateApiToken($request);
        return $this->fetchTokens($request);
    }

    public function deleteToken(\WP_REST_Request $request) {
        $delete = $this->apiAuth->deleteApiTokens($request);
        return $this->fetchTokens($request);
    }
}
