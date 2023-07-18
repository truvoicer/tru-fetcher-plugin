<?php
namespace TruFetcher\Includes\Api\Controllers\App;

use TruFetcher\Includes\Api\Forms\Tru_Fetcher_Api_Form_Handler;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_User_Response;
use TruFetcher\Includes\Database\Tru_Fetcher_Database;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Saved_Items;
use TruFetcher\Includes\Email\Tru_Fetcher_Email;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Ratings;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Saved_Items;
use TruFetcher\Includes\Tru_Fetcher;

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
class Tru_Fetcher_Api_User_Controller extends Tru_Fetcher_Api_Controller_Base
{

    const STATUS_SUCCESS = "success";
    const MAX_RATING = 5;

    const AUTH_TYPES = ["google", "facebook", "wordpress"];
    const AUTH_TYPE_META_KEY = "auth_type";
    const AUTH_TYPE_META_VALUE = "wordpress";

    const USER_ACCOUNT_FIELDS = ["user_id", "user_nicename", "display_name", "user_login", "user_email", "user_registered"];

    private Tru_Fetcher_Api_User_Response $apiUserResponse;
    private Tru_Fetcher_Email $emailManager;

    private Tru_Fetcher_Api_Helpers_Saved_Items $savedItemsHelper;
    private Tru_Fetcher_Api_Helpers_Ratings $ratingsHelper;

    protected ?string $namespace = "/users";
    private $options;

    public function __construct()
    {
        parent::__construct();
        $this->options = get_fields_clone("option");
        $this->emailManager = new Tru_Fetcher_Email();
        $this->savedItemsHelper = new Tru_Fetcher_Api_Helpers_Saved_Items();
        $this->ratingsHelper = new Tru_Fetcher_Api_Helpers_Ratings();
    }

    public function init()
    {
        $this->loadResponseObjects();
        add_action('rest_api_init', [$this, "register_routes"]);
    }

    private function loadResponseObjects()
    {
        $this->apiUserResponse = new Tru_Fetcher_Api_User_Response();
    }

    public function register_routes()
    {
        register_rest_route($this->publicEndpoint, '/create', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "createUser"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->protectedEndpoint, '/account/data/request', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "userDataRequest"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->protectedEndpoint, '/update', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "updateUser"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->publicEndpoint, '/password-reset', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "passwordReset"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->publicEndpoint, '/password-reset/validate', array(
            'methods' => \WP_REST_Server::READABLE,
            'callback' => [$this, "passwordResetValidate"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->protectedEndpoint, '/item/save', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "saveItem"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->protectedEndpoint, '/item/rating/save', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "saveItemRating"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->protectedEndpoint, '/item/list', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "getItemListData"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
        register_rest_route($this->protectedEndpoint, '/item/list-by-user', array(
            'methods' => \WP_REST_Server::CREATABLE,
            'callback' => [$this, "getItemListDataByUser"],
            'permission_callback' => [$this->apiAuthApp, 'allowRequest']
        ));
    }

    public function createUser($request)
    {
        $username = $request["username"];
        $email = $request["email"];
        $password = $request["password"];

        $createUser = wp_create_user($username, $password, $email);
        if (is_wp_error($createUser)) {
            return $this->showError($createUser->get_error_code(), $createUser->get_error_message());
        }
        wp_new_user_notification($createUser, null, "user");
        update_user_meta($createUser, self::AUTH_TYPE_META_KEY, self::AUTH_TYPE_META_VALUE);
        $apiFormHandler = new Tru_Fetcher_Api_Form_Handler();
        $apiFormHandler->processEndpointProvidersByRequest($request);
        $getUserData = [
            "username" => $username,
            "email" => $email,
            "redirect_url" => isset($request["redirect_url"])? $request["redirect_url"] : false
        ];

        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                sprintf("Confirmation email has been sent to (%s). Click on the confirmation link in the email to complete registration.", $email),
                $getUserData)
        );
    }

    private function getUserDataArray(\WP_User $user, array $requestData = []) {
        $userData = [];
        foreach($requestData as $key => $item) {
            $objectKey = $item["name"];
            if (in_array($objectKey, self::USER_ACCOUNT_FIELDS) && isset($user->data->$objectKey)) {
                $userData[$objectKey] = $user->data->$objectKey;
            }
        }
        return $userData;
    }

    public function userDataRequest($request)
    {
        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }
        $userData = $this->getUserDataArray($getUser, $request["form"]["fields"]);
        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                sprintf("User (%s) updated.", $getUser->user_email),
                $userData)
        );
    }

    public function updateUser($request)
    {
        $userData = [];
        $userData["ID"] = $request["user_id"];
        $userData["user_nicename"] = $request["user_nicename"];
        $userData["display_name"] = $request["display_name"];
        $userData["user_login"] = $request["user_login"];
        $userData["user_email"] = $request["user_email"];

        if (!in_array($request["auth_type"], self::AUTH_TYPES)) {
            return $this->showError("auth_type_invalid", "Invalid authentication type in request.");
        }

        if (isset($request["change_password"]) && $request["change_password"]) {
            if ($request["auth_type"] !== self::AUTH_TYPE_META_VALUE) {
                return $this->showError(
                    "auth_type_not_allowed",
                    sprintf("Authentication (%s) password change not allowed.", $request["auth_type"])
                );
            }
            $authenticateUser = wp_authenticate($userData["user_email"], $request["current_password"]);
            if (is_wp_error($authenticateUser)) {
                return $this->showError($authenticateUser->get_error_code(), $authenticateUser->get_error_message());
            }
            if ($request["confirm_password"] === $request["new_password"]) {
                $userData["user_pass"] = $request["new_password"];
            }
        }

        $updateUser = wp_update_user($userData);
        if (is_wp_error($updateUser)) {
            return $this->showError($updateUser->get_error_code(), $updateUser->get_error_message());
        }

        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                sprintf("User (%s) updated.", $userData["user_nicename"]),
                $userData)
        );
    }

    public function passwordReset($request)
    {
        if (!isset($request["username"])) {
            return $this->showError("request_missing_parameters", "Username or email not in request.");
        }
        if ($request["username"] === "" || $request["username"] === null) {
            return $this->showError("invalid_request", "Username or email is invalid.");
        }

        $getUser = get_user_by_email($request["username"]);
        if (!$getUser) {
            $getUser = get_user_by("username", $request["username"]);
        }
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }

        $getPasswordResetKey = get_password_reset_key($getUser);
        if (is_wp_error($getPasswordResetKey)) {
            return $this->showError($getPasswordResetKey->get_error_code(), $getPasswordResetKey->get_error_message());
        }

        $sendPasswordResetKey = $this->emailManager->sendEmail(
            $getUser->user_email,
            sprintf("Password Reset | %s", get_option("blogname")),
            "password-reset-key",
            [
                "EMAIL_TITLE" => sprintf("Password Reset | %s", get_option("blogname")),
                "USERNAME" => $getUser->user_login,
                "USER_EMAIL" => $getUser->user_email,
                "PASSWORD_RESET_URL" => $this->getPasswordResetKeyUrl($getPasswordResetKey, $getUser->ID)
            ]
        );
        if (!$sendPasswordResetKey) {
            return $this->showError(
                "send_password_reset_key_error",
                "There was an error sending the password reset to your email. Please try again."
            );
        }
        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                sprintf("An email has been sent to your inbox (%s). Please follow the instructions.", $getUser->user_email),
                [])
        );
    }

    public function passwordResetValidate($request)
    {
        if (!isset($request["reset_key"]) || !isset($request["user_id"])) {
            return $this->showError("request_missing_parameters", "Password reset key or user_id not in request.");
        }
        if ($request["reset_key"] === "" || $request["reset_key"] === null) {
            return $this->showError("invalid_request", "Password reset key is invalid.");
        }
        if ($request["user_id"] === "" || $request["user_id"] === null) {
            return $this->showError("invalid_request", "User Id is invalid.");
        }
        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }

        $validateResetKey = check_password_reset_key($request["reset_key"], $getUser->user_login);
        if (is_wp_error($validateResetKey)) {
            return $this->showError($validateResetKey->get_error_code(), $validateResetKey->get_error_message());
        }

        $generatePassword = wp_generate_password();
        $userData = [
            "ID" => $validateResetKey->ID,
            "user_pass" => $generatePassword,
        ];
        $updateUser = wp_update_user($userData);

        if (is_wp_error($updateUser)) {
            return $this->showError($updateUser->get_error_code(), $updateUser->get_error_message());
        }
        $sendPasswordResetKey = $this->emailManager->sendEmail(
            $validateResetKey->user_email,
            sprintf("Password Reset | %s", get_option("blogname")),
            "password-reset-success",
            [
                "EMAIL_TITLE" => sprintf("Password Reset | %s", get_option("blogname")),
                "USERNAME" => $validateResetKey->user_login,
                "USER_EMAIL" => $validateResetKey->user_email,
                "NEW_PASSWORD" => $generatePassword
            ]
        );
        if (!$sendPasswordResetKey) {
            return $this->showError(
                "send_password_reset_key_error",
                "There was an error sending a new password to your email. Please submit the password request again."
            );
        }
        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                sprintf("A temporary password has been sent to your inbox (%s). Please follow the instructions.", $validateResetKey->user_email),
                [])
        );
    }

    private function getPasswordResetKeyUrl($passwordResetKey, $userId)
    {
        return sprintf("%s/auth/password-reset/%s/%s", Tru_Fetcher::getFrontendUrl(), $userId, $passwordResetKey);
    }

    private function getUserItemRequestData($request)
    {
        $date = new \DateTime();
        $data = [];
        $data["provider_name"] = $request["provider_name"];
        $data["user_id"] = $request["user_id"];
        $data["category"] = $request["category"];
        $data["item_id"] = $request["item_id"];
        $data["date_created"] = $date->format("Y-m-d H:i:s");
        return $data;
    }

    public function saveItem($request)
    {
        $data = $this->getUserItemRequestData($request);

        $dbClass = new Tru_Fetcher_Database();
        $getItem = $dbClass->getUserItemRow(
            Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME,
            $data["provider_name"], $data["category"], $data["item_id"], $data["user_id"]
        );
        if ($getItem === null) {
            $dbClass->insertData(Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME, $data);
        } else {
            $dbClass->delete(Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME, "item_id=%s", [$data["item_id"]]);
        }

        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                "",
                true)
        );
    }

    public function saveItemRating($request)
    {
        $data = $this->getUserItemRequestData($request);
        $data["rating"] = $request["rating"];

        $dbClass = new Tru_Fetcher_Database();
        $getItem = $dbClass->getUserItemRow(
            Tru_Fetcher_Database::RATINGS_TABLE_NAME,
            $data["provider_name"], $data["category"], $data["item_id"], $data["user_id"]
        );
        if ($getItem === null) {
            $dbClass->insertData(Tru_Fetcher_Database::RATINGS_TABLE_NAME, $data);
        } else {
            $dbClass->updateData(
                Tru_Fetcher_Database::RATINGS_TABLE_NAME,
                ["rating" => "%d"],
                ["item_id" => "%s", "user_id" => "%d"],
                [(int)$data["rating"]],
                ['"' . $data["item_id"] . '"', $data["user_id"]]
            );
        }

        $getRatings = $this->getRatingsData(
            $request["provider_name"],
            $request["category"],
            [$data["item_id"]],
            $request["user_id"]
        );

        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                "",
                $getRatings)
        );
    }

    private function getStringCount($array, $string)
    {
        $str = "";
        foreach ($array as $value) {
            $str .= sprintf("'%s',", $string);
        }

        return rtrim($str, ',');
    }

    public function getItemListDataByUser($request)
    {
        $data = [];
        $data["user_id"] = $request["user_id"];

        $dbClass = new Tru_Fetcher_Database();
        $where = "user_id=%s";
        $getResults = $dbClass->getResults(
            Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME,
            $where,
            $data["user_id"]
        );

        if (isset($request["internal_provider_name"])) {
            $internalProviderName = $request["internal_provider_name"];
            $getResults = array_map(function ($item) use ($internalProviderName) {
                if ($internalProviderName !== $item->provider_name) {
                    return $item;
                }
                $item->data = \get_fields_clone((int)$item->item_id);
                return $item;
            }, $getResults);
        }

        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                "",
                $getResults)
        );
    }

    public function getItemListData($request)
    {

        $getSavedItems = $this->getSavedItemsData(
            $request["provider_name"],
            $request["category"],
            $request["id_list"],
            $request["user_id"]
        );
        $getRatings = $this->getRatingsData(
            $request["provider_name"],
            $request["category"],
            $request["id_list"],
            $request["user_id"]
        );

        return $this->sendResponse(
            $this->buildResponseObject(self::STATUS_SUCCESS,
                "",
                [
                    "saved_items" => $getSavedItems,
                    "item_ratings" => $getRatings
                ]
            )
        );
    }

    private function getSavedItemsData($providerName, $category, $idList, $user_id)
    {
        if (count($idList) === 0) {
            return [];
        }

        return $this->savedItemsHelper->getSavedItemsRepository()->fetchByItemIdBatch(
            $this->apiAuthApp->getUser(),
            $providerName,
            $category,
            $idList
        );
    }

    private function getRatingsData($providerName, $category, $idList, $user_id)
    {
        if (count($idList) === 0) {
            return [];
        }
        $dbClass = new Tru_Fetcher_Database();
        $getRatings = [];
        foreach ($idList as $item) {
            $rating = null;
            $getItemRating = $dbClass->getRow(
                Tru_Fetcher_Database::RATINGS_TABLE_NAME,
                "provider_name=%s AND category=%s AND user_id=%s AND item_id=%s",
                $providerName, $category, $user_id, $item
            );
            if ($getItemRating === null) {
                continue;
            } else {
                $rating = $getItemRating->rating;
            }
            $overallRating = $this->getItemOverallRating($getItemRating);
            if ($overallRating !== null) {
                $getItemRating->overall_rating = $overallRating["overall_rating"];
                $getItemRating->total_users_rated = $overallRating["total_users_rated"];
            }

            $getItemRating->rating = $rating;
            $getItemRating->user_id = $user_id;
            array_push($getRatings, $getItemRating);

        }
        return $getRatings;
    }

    private function getItemOverallRating($data)
    {
        $dbClass = new Tru_Fetcher_Database();
        $where = "provider_name=%s AND category=%s AND item_id=%s";
        $getTotal = $dbClass->getTotalUserRating(
            Tru_Fetcher_Database::RATINGS_TABLE_NAME,
            "rating",
            $where,
            [$data->item_id, $data->provider_name, $data->category, $data->item_id]
        );
        if ($getTotal === null || !isset($getTotal->rating) || !isset($getTotal->total_users_rated)) {
            return null;
        }
        $maxUserRatingCount = (int)$getTotal->total_users_rated * self::MAX_RATING;
        $calculateRating = ((int)$getTotal->rating * self::MAX_RATING) / $maxUserRatingCount;
        $roundUpToInteger = ceil($calculateRating);
        return [
            "overall_rating" => $roundUpToInteger,
            "total_users_rated" => (int)$getTotal->total_users_rated
        ];
    }

    private function buildResponseObject($status, $message, $data)
    {
        $this->apiUserResponse->setStatus($status);
        $this->apiUserResponse->setMessage($message);
        $this->apiUserResponse->setData($data);

        return $this->apiUserResponse;
    }

    private function sendResponse(Tru_Fetcher_Api_User_Response $api_user_response)
    {
        return rest_ensure_response($api_user_response);
    }
}
