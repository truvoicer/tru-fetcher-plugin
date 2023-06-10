<?php

namespace TruFetcher\Includes\Api\Auth;

use Carbon\Carbon;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Token_Response;
use TruFetcher\Includes\Tru_Fetcher_Base;
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
class Tru_Fetcher_Api_Auth_Admin extends Tru_Fetcher_Api_Auth
{
    public function __construct()
    {
        parent::__construct();
        $this->authJwt->setSecret($this->getReactSecretKey());
    }

    public function nonceRequestHandler(\WP_REST_Request $request)
    {
        $appKey = $request->get_param('app_key');
        if (!$appKey) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_app_key',
                "App key is required"
            );
        }
        $init = $this->requestInit($request);
        if (is_wp_error($init)) {
            return $init;
        }
        $decodePayloadAction = $this->authJwt->jwtDecode(
            'nonce',
            $appKey,
            $this->getUser(),
            $this->getPayloadJwt(),
        );

        $userMetaEncodedNonce = get_user_meta($this->getUser()->ID, 'nonce_jwt', true);

        $decodeUserMetaEncodedNonce = $this->authJwt->jwtDecode(
            'nonce',
            $appKey,
            $this->getUser(),
            $userMetaEncodedNonce,
        );

        if ($decodePayloadAction['payload']['nonce'] !== $decodeUserMetaEncodedNonce['payload']['nonce']) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_nonce',
                'Nonce is invalid'
            );
        }
        $nonce = $decodeUserMetaEncodedNonce['payload']['nonce'];

        $nonceActionName = $this->authJwt->getJwtKey('nonce', $appKey, $this->getUser());
        $md5NonceAction = md5($nonceActionName);

        add_filter('nonce_user_logged_out', function ($uid, $action) use ($md5NonceAction) {
            if ($action !== $md5NonceAction) {
                return false;
            }
            return $this->getUser()->ID;
        }, 10, 2);

        if (!wp_verify_nonce($nonce, $md5NonceAction)) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_nonce',
                'Error verifying nonce'
            );
        }
        return true;
    }

    public function tokenRequestHandler(\WP_REST_Request $request)
    {
        $appKey = $request->get_param('app_key');
        if (!$appKey) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_app_key',
                "App key is required"
            );
        }
        $validateNonce = $this->nonceRequestHandler($request);
        if (is_wp_error($validateNonce)) {
            return $validateNonce;
        }
        $validateBearerToken = $this->validateBearerToken($request);
        if (is_wp_error($validateBearerToken)) {
            return $validateBearerToken;
        }
        $validateUserToken = $this->validateUserToken($appKey);
        if (is_wp_error($validateUserToken)) {
            return $validateUserToken;
        }

        if ($this->getBearerToken() !== $this->userToken[$this->authJwt::TOKEN]) {
            return false;
        }

        $expiresAt = $this->getUserToken()[$this->apiTokenModel->getExpiresAtColumn()];
        $expiresAtTimestamp = strtotime($expiresAt);
        if (Carbon::now()->timestamp > ($expiresAtTimestamp - 3600)) {
            return false;
        }
        return true;
    }

    public function updateApiToken(\WP_REST_Request $request) {
        $id = $request->get_param('id');
        $expiresAt = $request->get_param('expires_at');

        if (!$id || !$expiresAt) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_update_api_token',
                'Missing required parameters, id or expires_at'
            );
        }

        $expiresAtDatetime = wp_date('Y-m-d H:i:s', strtotime($expiresAt));
        return $this->apiTokensRepository->updateApiToken($id, $expiresAtDatetime);
    }

    public function deleteApiTokens(\WP_REST_Request $request) {
        $data = self::getApiTokensRequestData($request);
        return $this->apiTokensRepository->deleteApiTokens($data);
    }

    public function fetchRoles() {
        $roles = [];
        foreach ($this->wpRoles->get_names() as $name => $label) {
            $roles[] = [
                'name' => $name,
                'label' => $label,
            ];
        }
        return $roles;
    }
}
