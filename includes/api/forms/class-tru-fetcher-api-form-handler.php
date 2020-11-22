<?php
require_once plugin_dir_path(dirname(__FILE__)) . 'controllers/class-tru-fetcher-api-controller-base.php';

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
class Tru_Fetcher_Api_Form_Handler extends Tru_Fetcher_Api_Controller_Base
{

    private Tru_Fetcher_Api_Forms_Response $apiFormsResponse;

    public function __construct()
    {
        $this->load_dependencies();
        $this->loadResponseObjects();
        add_filter('mime_types', [$this, 'defineAllowedMimeTypes']);
    }

    public function defineAllowedMimeTypes($existing_mimes)
    {
        // Add webm to the list of mime types. $existing_mimes['webm'] = 'video/webm';
        // Return the array back to the function with our added mime type.
        $existing_mimes["png"] = "image/png";
        $existing_mimes["jpg"] = "image/jpg";
        $existing_mimes["jpeg"] = "image/jpeg";
        return $existing_mimes;
    }

    private function load_dependencies()
    {
        if (!class_exists("Tru_Fetcher_Api_Forms_Response")) {
            require_once plugin_dir_path(dirname(__FILE__)) . 'response/ApiFormsResponse.php';
        }
    }

    private function loadResponseObjects()
    {
        $this->apiFormsResponse = new Tru_Fetcher_Api_Forms_Response();
    }

    public function fetchUserMetaData(WP_REST_Request $request)
    {
        $data = $request->get_params();
        $userData = $this->getFormBuilderUserMetaData($data["form"]);

        return $this->sendResponse(
            $this->buildResponseObject(
                self::STATUS_SUCCESS,
                sprintf("User (%s) data fetched.", $this->getUser()->display_name),
                $userData)
        );
    }

    private function getFormBuilderUserMetaData(array $form = []) {
        switch ($form["type"]) {
            case "single":
                return $this->getSingleFormTypeUserMetaData($form);
            case "list":
                return $this->getListFormTypeUserMetaData($form);
            default:
                return false;
        }
    }

    private function getListFormTypeUserMetaData(array $form = []) {
        $listFormMetaData = get_user_meta($this->getUser()->ID, $form["id"], true);
        return [
          $form["id"] => (!$listFormMetaData || $listFormMetaData === "")? [] : $listFormMetaData
        ];
    }

    private function getSingleFormTypeUserMetaData(array $form = []) {
        return $this->buildUserMetaDataArray($form["fields"]);
    }

    private function buildUserMetaDataArray(array $data = []) {
        $userData = [];
        foreach ($data as $key => $field) {
            if (array_key_exists("form_control", $field)) {
                $userData[$field["name"]] = $this->getFormFieldUserMetaData($field);
            }
        }
        return $userData;
    }

    private function getFormFieldUserMetaData(array $field) {
        switch ($field["form_control"]) {
            case "file_upload":
            case "image_upload":
                return get_user_meta($this->getUser()->ID, $field["name"] . "_attachment_id", true);
            case "select_data_source":
                return apply_filters( "tfr_user_meta_select_data_source", $field, $this->getUser() );
            default:
                return get_user_meta($this->getUser()->ID, $field["name"], true);
        }
    }

    public function buildSelectList($valueKey, $labelKey, $data)
    {
        return array_map(function ($item) use ($valueKey, $labelKey) {
            return [
                "value" => $item->$valueKey,
                "label" => $item->$labelKey
            ];
        }, $data);
    }


    public function saveUserMetaData(WP_REST_Request $request)
    {
        $data = $request->get_params();

        $getUser = get_userdata($request["user_id"]);
        if (!$getUser) {
            return $this->showError("user_not_exist", "Sorry, this user does not exist.");
        }
        $this->saveUserProfileMeta(
            $getUser,
            $data
        );
        return $this->sendResponse(
            $this->buildResponseObject(
                self::STATUS_SUCCESS,
                sprintf("User (%s) updated.", $data["user_nicename"]),
                $data)
        );
    }

    public function saveUserProfileFileUploads(WP_User $user, array $filesArray = [])
    {
        if (!function_exists('wp_read_image_metadata')) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
        }
        if (!function_exists('wp_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/file.php');
        }
        if (!function_exists('media_handle_upload')) {
            require_once(ABSPATH . 'wp-admin/includes/media.php');
        }
        $errors = [];
        $attachments = [];
        foreach ($filesArray as $key => $file) {
            $mediaUpload = media_handle_upload($key, 0);
            if (is_wp_error($mediaUpload)) {
                array_push($errors, $mediaUpload);
                continue;
            }
            update_user_meta(
                $user->ID,
                "{$key}_attachment_id",
                $mediaUpload
            );
            array_push($attachments, [
                "attachment_id" => $mediaUpload,
                "file_name" => $key
            ]);
        }
        return [
            "errors" => $errors,
            "attachments" => $attachments
        ];
    }

    public function saveUserProfileMeta(WP_User $user, array $profileData = [])
    {
        $errors = [];
        foreach ($profileData as $key => $value) {
            $updateUserMeta = update_user_meta(
                $user->ID,
                $key,
                $value
            );
            if (!$updateUserMeta) {
                array_push($errors, $key);
            }
        }
        if (count($errors) > 0) {
            return $errors;
        }
        return true;
    }

    private function buildResponseObject($status, $message, $data)
    {
        $this->apiFormsResponse->setStatus($status);
        $this->apiFormsResponse->setMessage($message);
        $this->apiFormsResponse->setData($data);

        return $this->apiFormsResponse;
    }

    private function sendResponse(Tru_Fetcher_Api_Forms_Response $apiFormsResponse)
    {
        return rest_ensure_response($apiFormsResponse);
    }

}
