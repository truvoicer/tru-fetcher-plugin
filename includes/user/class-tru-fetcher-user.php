<?php

namespace TruFetcher\Includes\User;

use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Token_Response;
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
class Tru_Fetcher_User
{
    private Tru_Fetcher_DB_Repository_Api_Tokens $apiTokensRepository;

    public function __construct()
    {
        $this->apiTokensRepository = new Tru_Fetcher_DB_Repository_Api_Tokens();
    }

    public static function getUserById(int $userId)
    {
        if (!isset($userId)) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_userid',
                'UserId is invalid in request'
            );
        }
        $user = get_userdata($userId);
        if (!$user) {
            return new \WP_Error(
                Tru_Fetcher_Api_Admin_Token_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_user',
                sprintf(
                    'User id %s does not exist',
                    $user
                )
            );
        }
        return $user;
    }

    public function deleteUserHandler(int $id, int|null $reassign, \WP_User $user)
    {
        $deleteApiTokens = $this->apiTokensRepository->deleteApiTokensByUserId($id);
        if (!$deleteApiTokens) {
            error_log(json_encode(['message' => 'Error deleting user api tokens', 'user_id' => $id]));
        }
    }

    public static function getUserMetaData(\WP_User $user, array $data) {
        foreach ($data as $key => $value) {
            $data[$key] = get_user_meta($user->ID, $key, true);
        }
        return $data;
    }
}
