<?php

namespace TruFetcher\Includes\Api\Auth;

use Carbon\Carbon;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Token_Response;
use TruFetcher\Includes\Firebase\Helpers\Tru_Fetcher_Firebase_Helpers;
use TruFetcher\Includes\Firebase\Tru_Fetcher_Firebase_Messaging;
use TruFetcher\Includes\Tru_Fetcher_Base;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\User\Tru_Fetcher_User;
use WP_Error;

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
class Tru_Fetcher_Api_Auth_App extends Tru_Fetcher_Api_Auth
{
    use Tru_Fetcher_Traits_Errors;
    const AUTH_PROVIDER = 'auth_provider';
    const AUTH_PROVIDER_FACEBOOK = 'facebook';
    const AUTH_PROVIDER_GOOGLE = 'google';
    const AUTH_PROVIDER_WORDPRESS = 'wordpress';

    private Tru_Fetcher_Firebase_Helpers $firebaseHelpers;

    public function __construct()
    {
        parent::__construct();
        $this->authJwt->setSecret($this->getAppSecretKey());
        $this->firebaseHelpers = new Tru_Fetcher_Firebase_Helpers();
    }


    public function publicTokenRequestHandler(\WP_REST_Request $request)
    {
        $validateBearerToken = $this->validateBearerToken($request);
        if (is_wp_error($validateBearerToken)) {
            return $validateBearerToken;
        }
        if ($this->getBearerToken() !== $this->generatePublicAppToken()) {
            return false;
        }
        return true;
    }

    public function protectedTokenRequestHandler(\WP_REST_Request $request)
    {
        $validateBearerToken = $this->validateBearerToken($request);
        if (is_wp_error($validateBearerToken)) {
            return $validateBearerToken;
        }

        $validateUserToken = $this->validateAppUserToken();
        if (is_wp_error($validateUserToken)) {
            return $validateUserToken;
        }
        return true;
    }

    protected function validateAppUserToken() {
        $generateToken = $this->authJwt->jwtRawDecode($this->getBearerToken());
        if (is_wp_error($generateToken)) {
            return $generateToken;
        }
        $tokenPayload = $this->authJwt->getPayload($generateToken);
        if (is_wp_error($tokenPayload)) {
            return $tokenPayload;
        }
        if (!isset($tokenPayload['user_id'])) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_validate',
                'User id not found'
            );
        }
        if (!isset($tokenPayload['type'])) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_validate',
                'Type not found'
            );
        }

        $user = Tru_Fetcher_User::getUserById($tokenPayload['user_id']);
        if (is_wp_error($user)) {
            return $user;
        }

        $this->setUser($user);
        $getUserToken = $this->buildUserToken($tokenPayload['type']);
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
        if ($this->getBearerToken() !== $this->userToken[$this->apiTokenModel->getTokenColumn()]) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_validate',
                '`Token not match'
            );
        }

        $expiresAt = $this->getUserToken()[$this->apiTokenModel->getExpiresAtColumn()];
        $expiresAtTimestamp = strtotime($expiresAt);
        if (Carbon::now()->timestamp > ($expiresAtTimestamp - 3600)) {
            return false;
        }
        return true;
    }
    public function generatePublicAppToken()
    {
        $jwtAuth = $this->getAuthJwt();
        $jwtAuth->setSecret($this->getAppSecretKey());
        return $jwtAuth->jwtRawEncode(['type' => 'app']);
    }

    private function createWordpressUser(\WP_REST_Request $request, string $authProvider)
    {
        $email = $request->get_param('email');
        $username = $request->get_param('username');
        $password = $request->get_param('password');
        $passwordConfirmation = $request->get_param('password_confirmation');
        if (empty($email) || empty($username) || empty($password) || empty($passwordConfirmation)) {
            return new \WP_Error('auth_error', __('Email, username, password and password confirmation are required.', 'jwt-auth'));
        }
        if ($password !== $passwordConfirmation) {
            return new \WP_Error('auth_error', __('Passwords do not match.', 'jwt-auth'));
        }
        $userData = [
            "nickname" => $username,
            "user_nicename" => $username,
            "display_name" => $username
        ];
        return $this->createUser($authProvider, $username, $email, $userData, $password);
    }

    private function getPassword(string $authProvider, ?string $password = null) {
        switch ($authProvider) {
            case self::AUTH_PROVIDER_GOOGLE:
            case self::AUTH_PROVIDER_FACEBOOK:
                return wp_generate_password(16, true);
            case self::AUTH_PROVIDER_WORDPRESS:
                if (empty($password)) {
                    return new \WP_Error('auth_error', __('Password is required.', 'jwt-auth'));
                }
                return $password;
            default:
                return new \WP_Error('auth_error', 'Invalid auth provider');
        }
    }
    private function createUser(string $authProvider, string $username, string $email, array $userData, ?string $password = null)
    {
        $password = $this->getPassword($authProvider, $password);
        if (is_wp_error($password)) {
            return $password;
        }
        $createUser = wp_create_user($username, $password, $email);
        if (is_wp_error($createUser)) {
            return new \WP_Error('auth_error', __('Error creating new user.', 'jwt-auth'));
        }
        $userData['ID'] = $createUser;
        wp_update_user($userData);
        update_user_meta($createUser, self::AUTH_PROVIDER, $authProvider);
        $getUser = get_userdata($createUser);
        if (is_wp_error($getUser)) {
            return new \WP_Error('auth_error', __('Error retrieving new user.', 'jwt-auth'));
        }

        return $getUser;
    }

    private function validateGoogleToken(\WP_REST_Request $request, string $authProvider)
    {
        $token = $request->get_param('token');
        $validateKey = file_get_contents(sprintf($this->googleValidateUrl, $token));
        if (!$validateKey) {
            return new WP_Error('auth_error', __('Validation failed, invalid Google auth key.', 'jwt-auth'));
        }

        $validateObject = json_decode($validateKey);
        $userEmail = $validateObject->email;
        $getUser = get_user_by_email($userEmail);

        if (!$getUser) {
            $userData = [
                "nickname" => $userEmail,
                "user_nicename" => $userEmail,
                "display_name" => $userEmail,
                "first_name" => $validateObject->given_name,
                "last_name" => $validateObject->family_name,
                'picture' => $validateObject->picture
            ];
            $getUser = $this->createUser(
                $authProvider,
                $userEmail,
                $userEmail,
                $userData
            );
            if (is_wp_error($getUser)) {
                return $getUser;
            }
        }

        return $getUser;
    }

    private function validateFacebookToken(\WP_REST_Request $request, string $authProvider)
    {
        $token = $request->get_param('token');
        $config = parent::getConfig(self::FACEBOOK_CONFIG);
        try {
            $fb = new \Facebook\Facebook([
                'app_id' => $config->facebook_sdk->app_id,
                'app_secret' => $config->facebook_sdk->app_secret,
                'default_graph_version' => $config->facebook_sdk->graph_version,
            ]);
            // The OAuth 2.0 client handler helps us manage access tokens
            $oAuth2Client = $fb->getOAuth2Client();

            // Get the access token metadata from /debug_token
            $tokenMetadata = $oAuth2Client->debugToken($token);
            if (!$tokenMetadata->getIsValid()) {
                return new \WP_Error('auth_error', $tokenMetadata->getErrorMessage());
            }

            $getFbUser = $fb->get('/me?fields=id,name,first_name,last_name,email,picture', $token);
            if (!array_key_exists("email", $getFbUser->getDecodedBody())) {
                return new \WP_Error('auth_error', __('Error pulling email address from facebook.', 'jwt-auth'));
            }
            $email = $getFbUser->getDecodedBody()["email"];
            $getUser = get_user_by_email($email);

            $userData = [];
            if (array_key_exists("first_name", $getFbUser->getDecodedBody())) {
                $userData['first_name'] = $getFbUser->getDecodedBody()["first_name"];
            }
            if (array_key_exists("last_name", $getFbUser->getDecodedBody())) {
                $userData['last_name'] = $getFbUser->getDecodedBody()["last_name"];
            }
            if (array_key_exists("picture", $getFbUser->getDecodedBody())) {
                $userData['picture'] = $getFbUser->getDecodedBody()["picture"]["data"]["url"];
            }
            $userData['nickname'] = $email;
            $userData['user_nicename'] = $email;
            $userData['display_name'] = $email;
            if (!$getUser) {
                $getUser = $this->createUser(
                    $authProvider,
                    $email,
                    $email,
                    $userData
                );
                if (is_wp_error($getUser)) {
                    return $getUser;
                }
            }
        } catch (\Facebook\Exceptions\FacebookSDKException $e) {
            return new \WP_Error('auth_error', $e->getMessage());
        }

        return $getUser;
    }

    public function registerRequestHandler(\WP_REST_Request $request)
    {
        $authProvider = $request->get_param(self::AUTH_PROVIDER);
        switch ($authProvider) {
            case self::AUTH_PROVIDER_GOOGLE:
                $user = $this->validateGoogleToken($request, $authProvider);
                break;
            case self::AUTH_PROVIDER_FACEBOOK:
                $user = $this->validateFacebookToken($request, $authProvider);
                break;
            case self::AUTH_PROVIDER_WORDPRESS:
                $user = $this->createWordpressUser($request, $authProvider);
                break;
            default:
                return new \WP_Error('auth_error', 'Invalid auth provider');
        }

        if (is_wp_error($user)) {
            return $user;
        }

        $this->setUser($user);

        if (!$this->firebaseHelpers->validateUserDeviceFromRequest($request, $user)) {
            $this->setErrors(
                array_merge(
                    $this->getErrors(),
                    $this->firebaseHelpers->getErrors()
                ));
        }

        return $this->generateToken($authProvider);
    }

    public function wordpressLoginHandler(\WP_REST_Request $request, $authProvider) {
        $username = $request->get_param('username');
        $password = $request->get_param('password');
        if (empty($username) || empty($password)) {
            return new \WP_Error('auth_error', __('Username, password are required.', 'jwt-auth'));
        }
        $validateUsername = validate_username($username);
        if (!$validateUsername) {
            return new \WP_Error('auth_error', __('Invalid username.', 'jwt-auth'));
        }
        $user = get_user_by('login', $username);
        if (!$user) {
            return new \WP_Error('auth_error', __('Invalid username.', 'jwt-auth'));
        }
        if(!wp_check_password($password, $user->data->user_pass, $user->ID)) {
            return new \WP_Error('auth_error', __('Invalid password.', 'jwt-auth'));
        }
        return $user;
    }
    public function loginRequestHandler(\WP_REST_Request $request)
    {
        $authProvider = $request->get_param(self::AUTH_PROVIDER);

        switch ($authProvider) {
//            case self::AUTH_PROVIDER_GOOGLE:
//                $user = $this->validateGoogleToken($request, $authProvider);
//                break;
//            case self::AUTH_PROVIDER_FACEBOOK:
//                $user = $this->validateFacebookToken($request, $authProvider);
//                break;
            case self::AUTH_PROVIDER_WORDPRESS:
                $user = $this->wordpressLoginHandler($request, $authProvider);
                break;
            default:
                return new \WP_Error('auth_error', 'Invalid auth provider');
        }

        if (is_wp_error($user)) {
            return $user;
        }

        $this->setUser($user);

        if (!$this->firebaseHelpers->validateUserDeviceFromRequest($request, $user)) {
            $this->setErrors(
                array_merge(
                    $this->getErrors(),
                    $this->firebaseHelpers->getErrors()
            ));
        }

        $fetchApiToken = $this->apiTokensRepository->getUserToken($authProvider, $user);
        if (!$fetchApiToken) {
            return $this->generateToken($authProvider);
        }
        if (!isset($fetchApiToken[$this->apiTokenModel->getExpiresAtColumn()])) {
            return $this->generateToken($authProvider);
        }
        $expiry = $fetchApiToken[$this->apiTokenModel->getExpiresAtColumn()];
        $expiryDateTime = strtotime($expiry);
        if ($expiryDateTime < time()) {
            return $this->generateToken($authProvider);
        }
        return $fetchApiToken;
    }

    public function generateToken(string $tokenType)
    {
        $user = $this->getUser();
        $jwtAuth = $this->getAuthJwt();

        $issuedAt = wp_date('Y-m-d H:i:s');
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 day', strtotime($issuedAt)));

        $generateToken = $jwtAuth->jwtRawEncode([
            'user_id' => $user->ID,
            'type' => $tokenType,
            $jwtAuth::ISSUED_AT => strtotime($issuedAt),
            $jwtAuth::EXPIRES_AT => strtotime($expiresAt),
        ]);
        return $this->saveUserToken(
            $tokenType,
            $generateToken,
            $issuedAt,
            $expiresAt
        );
    }
}
