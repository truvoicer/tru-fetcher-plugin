<?php

namespace TruFetcher\Includes\Api\Auth;

use Carbon\Carbon;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Token_Response;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Api_Tokens;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Api_Tokens;
use TruFetcher\Includes\Tru_Fetcher_Base;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_User;
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
class Tru_Fetcher_Api_Auth extends Tru_Fetcher_Base
{
    use Tru_Fetcher_Traits_User;
    protected const TOKEN_KEY_PREFIX = 'api_token_';
    const JWT_KEY_TYPE ='api_token';
    const API_TOKENS_REQUEST_KEY = 'api_tokens';
    const ROLES_REQUEST_KEY = 'roles';
    protected \WP_Roles $wpRoles;
    protected Tru_Fetcher_Api_Auth_Jwt $authJwt;

    protected Tru_Fetcher_DB_Repository_Api_Tokens $apiTokensRepository;

    protected string $payloadJwt;
    protected string $bearerToken;
    protected array $requiredTokenDataKeys;
    protected array $userToken;
    protected string $tokenType;

    protected Tru_Fetcher_DB_Model_Api_Tokens $apiTokenModel;

    public function __construct()
    {
        parent::__construct();
        $this->apiTokensRepository = new Tru_Fetcher_DB_Repository_Api_Tokens();
        $this->apiTokenModel = new Tru_Fetcher_DB_Model_Api_Tokens();
        $this->authJwt = new Tru_Fetcher_Api_Auth_Jwt();
        $this->wpRoles = new \WP_Roles();
        $this->setRequiredTokenDataKeys($this->apiTokenModel->getTableColumns());
    }

    public static function getApiTokensRequestData(\WP_REST_Request $request)
    {
        return $request->get_param(self::API_TOKENS_REQUEST_KEY);
    }
    public static function getApiRolesRequestData(\WP_REST_Request $request)
    {
        return $request->get_param(self::ROLES_REQUEST_KEY);
    }

    /**
     * @return array
     */
    public function getUserToken(): array
    {
        return $this->userToken;
    }

    /**
     * @param array $userToken
     */
    public function setUserToken(array $userToken): void
    {
        $this->userToken = $userToken;
    }

    /**
     * @return string
     */
    public function getPayloadJwt(): string
    {
        return $this->payloadJwt;
    }

    /**
     * @param string $payloadJwt
     */
    public function setPayloadJwt(string $payloadJwt): void
    {
        $this->payloadJwt = $payloadJwt;
    }

    /**
     * @return array
     */
    public function getRequiredTokenDataKeys(): array
    {
        return $this->requiredTokenDataKeys;
    }

    /**
     * @param array $requiredTokenDataKeys
     */
    public function setRequiredTokenDataKeys(array $requiredTokenDataKeys): void
    {
        $this->requiredTokenDataKeys = $requiredTokenDataKeys;
    }

    /**
     * @return Tru_Fetcher_Api_Auth_Jwt
     */
    public function getAuthJwt(): Tru_Fetcher_Api_Auth_Jwt
    {
        return $this->authJwt;
    }

    /**
     * @param Tru_Fetcher_Api_Auth_Jwt $authJwt
     */
    public function setAuthJwt(Tru_Fetcher_Api_Auth_Jwt $authJwt): void
    {
        $this->authJwt = $authJwt;
    }

    /**
     * @return string
     */
    public function getBearerToken(): string
    {
        return $this->bearerToken;
    }

    /**
     * @param string $bearerToken
     */
    public function setBearerToken(string $bearerToken): void
    {
        $this->bearerToken = $bearerToken;
    }

    public function requestInit(\WP_REST_Request $request) {
        $payload = $request->get_param('payload');

        if (!isset($payload)) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_payload',
                'Payload is invalid in request'
            );
        }
        $this->setPayloadJwt($payload);
        $user = Tru_Fetcher_User::getUserById($request->get_param('user_id'));
        if (!$user) {
            return $user;
        }
        $this->setUser($user);
        return true;
    }

    protected function processBearerToken(\WP_REST_Request $request)
    {
        $token = $this->getTokenFromHeader($request->get_header('Authorization'));
        if (is_wp_error($token)) {
            error_log(json_encode($request->get_headers()));
            return $token;
        }
        $this->setBearerToken($token);
        return true;
    }

    protected function validateTokenData(array $tokenData) {
        foreach (array_keys($tokenData) as $key) {
            if (!in_array($key, $this->requiredTokenDataKeys)) {
                return new \WP_Error(
                    Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . 'usermeta_data_invalid',
                    "{$key} should not exist in token usermeta data"
                );
            }
        }
        if (count($tokenData) === count($this->requiredTokenDataKeys)) {
            return true;
        }
        return new \WP_Error(
            Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . 'usermeta_data_invalid',
            'Token usermeta data is invalid'
        );
    }

    protected function validateUserToken(string $appKey) {
        $getUserToken = $this->buildUserToken($appKey);
        if (is_wp_error($getUserToken)) {
            return $getUserToken;
        }
        $this->setUserToken($getUserToken);
        $validateTokenData = $this->validateTokenData($getUserToken);
        if (is_wp_error($validateTokenData)) {
            return $validateTokenData;
        } elseif ($validateTokenData !== true) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_validate',
                'Unknown token validate error'
            );
        }
        return true;
    }

    public function validateBearerToken(\WP_REST_Request $request) {
        $processBearerToken = $this->processBearerToken($request);
        if (is_wp_error($processBearerToken)) {
            return $processBearerToken;
        }
        return true;
    }


    public function tokenRefreshHandler(\WP_REST_Request $request)
    {
        $validateTokenRequest = $this->validateBearerToken($request);
        if (!$validateTokenRequest) {
//            var_dump('toke req false');
            return false;
        }
        if (is_wp_error($validateTokenRequest)) {
//            var_dump('toke req err');
            return $validateTokenRequest;
        }
        return true;
    }

    protected function buildUserTokenName(string $tokenType) {
        return sprintf(
            '%s_%s',
            self::TOKEN_KEY_PREFIX,
            $tokenType
        );
    }

    public function buildUserToken(string $appKey)
    {
        $getUserToken = $this->apiTokensRepository->getUserToken(
            $appKey,
            $this->getUser(),
        );
        if (!$getUserToken) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . 'fetch',
                'Error fetching user token'
            );
        }
        return $getUserToken;
    }

    public function saveUserToken(string $tokenType, string $token, string $issuedAt, string $expiresAt)
    {
        $saveUserToken = $this->apiTokensRepository->insertApiToken(
            $this->getUser(),
            $tokenType,
            $token,
            $issuedAt,
            $expiresAt
        );
        if (!$saveUserToken) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_save',
                'Error saving token'
            );
        }
        return $saveUserToken;
    }


    public function generateToken(string $appKey)
    {
        $user = $this->getUser();
        $jwtAuth = $this->getAuthJwt();
        $issuedAt = wp_date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($issuedAt)));
        $tokenPayload = [
            'user_id' => $user->ID,
            $jwtAuth::ISSUED_AT => strtotime($issuedAt),
            $jwtAuth::EXPIRES_AT => strtotime($expiresAt)
        ];
        $generateToken = $jwtAuth->jwtEncode(self::JWT_KEY_TYPE, $appKey, $user, $tokenPayload);
        return $this->saveUserToken(
            $appKey,
            $generateToken,
            $issuedAt,
            $expiresAt
        );
    }


    protected function getTokenFromHeader($headerValue)
    {
        if ($headerValue === null || $headerValue === "") {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_token',
                'Empty authorization header'
            );
        }
        if (!substr($headerValue, 0, 7) === "Bearer ") {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_token',
                'Invalid Bearer token'
            );
        }
        return str_replace("Bearer ", "", $headerValue);
    }

    public function allowRequest() {
        return true;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Api_Tokens
     */
    public function getApiTokensRepository(): Tru_Fetcher_DB_Repository_Api_Tokens
    {
        return $this->apiTokensRepository;
    }

    /**
     * @return Tru_Fetcher_DB_Model_Api_Tokens
     */
    public function getApiTokenModel(): Tru_Fetcher_DB_Model_Api_Tokens
    {
        return $this->apiTokenModel;
    }

    /**
     * @param string $tokenType
     * @return Tru_Fetcher_Api_Auth
     */
    public function setTokenType(string $tokenType): self
    {
        $this->tokenType = $tokenType;
        return $this;
    }

}
