<?php
namespace TruFetcher\Includes\Api\Forms;

use Tru_Fetcher_Api_Providers_Hubspot;
use TruFetcher\Includes\Api\Controllers\Tru_Fetcher_Api_Controller_Base;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Forms_Response;
use TruFetcher\Includes\Database\Tru_Fetcher_Database;
use WP_REST_Request;
use WP_User;

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

    const GROUP_KEY_APPENDIX = "_group";

    private Tru_Fetcher_Api_Forms_Response $apiFormsResponse;

    public function __construct()
    {
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

    private function loadResponseObjects()
    {
        $this->apiFormsResponse = new Tru_Fetcher_Api_Forms_Response();
    }
    public function processEndpointProvidersByRequest(WP_REST_Request $request) {
        $formData = $request->get_params();
        $endpointProviders = $formData["endpoint_providers"];
        $processEndpointProviders = [];
        if (is_array($endpointProviders) && count($endpointProviders) > 0) {
            $processEndpointProviders = $this->formEndpointProvidersHandler($endpointProviders, $formData);
        }
        return $processEndpointProviders;
    }

    public function formEndpointProvidersHandler(array $providersArray = [], array $formData = [])
    {
        return array_map(function ($provider) use ($formData) {
            $result = [
                "provider" => $provider["provider"],
            ];
            if (!isset($provider["form_field_mapping"])) {
                $result["result"] = "No mappings";
                return $result;
            }
            $result["result"] = $this->handleEndpointProvider($provider, $formData);
            return $result;
        }, $providersArray);
    }

    private function handleEndpointProvider(array $provider = [], array $formData = [])
    {
        switch ($provider["provider"]) {
            case "hubspot":
                return $this->createHubspotContact($provider, $formData);
            default:
                return false;
        }
    }

    private function createHubspotContact(array $provider = [], array $formData = [])
    {
        $hubspotClient = new Tru_Fetcher_Api_Providers_Hubspot();
        return $hubspotClient->newContact(
            $this->buildEndpointProviderData($formData, $provider["form_field_mapping"])
        );
    }

    private function buildEndpointProviderData(array $formData = [], array $mappings = []) {
        $dataArray = [];
        foreach ($mappings as $item) {
            if (!isset($item["provider_field_name"]) && !isset($item["form_field_name"])) {
                continue;
            }
            if (!isset($formData[$item["form_field_name"]])) {
                continue;
            }
            $dataArray[$item["provider_field_name"]] = $formData[$item["form_field_name"]];
        }
        return $this->endpointProvidersSpecialFieldsFilter($dataArray);
    }

    private function endpointProvidersSpecialFieldsFilter(array $data = []) {
        $filteredData = $data;
        foreach ($data as $key => $value) {
            switch ($key) {
                case "full_name":
                case "name":
                    $splitName = explode(" ", $value, 2);
                    if (count($splitName) > 0) {
                        $filteredData["firstname"] = $splitName[0];
                        if (isset($splitName[1])) {
                            $filteredData["lastname"] = $splitName[1];
                        }
                    }
                    unset($filteredData[$key]);
            }
        }
        return $filteredData;
    }

    public function getFormsProgressData(WP_REST_Request $request)
    {
        if (!isset($request["form_field_groups"])) {
            return $this->showError("invalid_request", "(form_field_groups) fields does not exist in request");
        }
        $progressFieldGroups = apply_filters("tfr_form_progress_field_groups", $request["form_field_groups"], $this->getUser());

        $buildFieldsGroupArray = $this->buildFieldsGroupArray($request["form_field_groups"], $progressFieldGroups);

        $buildFormsProgressData = $this->buildFormsProgressData($buildFieldsGroupArray);

        $getOverallProgressPercent = $this->calculateOverallProgressPercent($buildFormsProgressData);

        return $this->sendResponse(
            $this->buildResponseObject(
                self::STATUS_SUCCESS,
                "Forms progress fetch successful.",
                [
                    "groups" => $buildFormsProgressData,
                    "overall_progress_percentage" => round($getOverallProgressPercent)
                ])
        );
    }

    private function buildFormsProgressData(array $fieldGroups)
    {
        return array_map(function ($group) {
            $emptyFields = $this->buildEmptyFieldsArray($group["fields"]);
            $group["empty_fields"] = $emptyFields;
            $group["fields_complete_percent"] = $this->calculateCompletedFieldsPercent(
                count($group["empty_fields"]),
                count($group["fields"])
            );
            $group["fields_incomplete_percent"] = $this->calculateIncompleteFieldsPercent(
                count($group["empty_fields"]),
                count($group["fields"])
            );
            $group["group_complete_percent"] = $this->calculateGroupCompletePercent(
                count($group["empty_fields"]),
                count($group["fields"]),
                $group["percentage"]
            );
            $group["group_incomplete_percent"] = $this->calculateGroupIncompletePercent(
                count($group["empty_fields"]),
                count($group["fields"]),
                $group["percentage"]
            );
            unset($group["fields"]);
            return $group;
        }, $fieldGroups);
    }

    private function calculateOverallProgressPercent(array $data)
    {
        $totalSetPercentage = [];
        $totalCompletedPercentage = [];
        foreach ($data as $group) {
            array_push($totalSetPercentage, $group["percentage"]);
            array_push($totalCompletedPercentage, $group["group_complete_percent"]);
        }
        return (array_sum($totalCompletedPercentage) / array_sum($totalSetPercentage)) * 100;
    }

    private function calculateGroupCompletePercent(int $emptyCount, int $totalFields, int $groupSetPercent)
    {
        $completed = $this->calculateCompletedFieldsPercent($emptyCount, $totalFields);
        return ($completed / 100) * $groupSetPercent;
    }

    private function calculateGroupIncompletePercent(int $emptyCount, int $totalFields, int $groupSetPercent)
    {
        $completed = $this->calculateIncompleteFieldsPercent($emptyCount, $totalFields);
        return ($completed / 100) * $groupSetPercent;
    }

    private function calculateCompletedFieldsPercent(int $emptyCount, int $totalFields)
    {
        $completed = ($totalFields - $emptyCount) / $totalFields;
        return $completed * 100;
    }

    private function calculateIncompleteFieldsPercent(int $emptyCount, int $totalFields)
    {
        $incomplete = $emptyCount / $totalFields;
        return $incomplete * 100;
    }

    private function buildEmptyFieldsArray(array $fields)
    {
        $emptyFields = [];
        foreach ($fields as $field) {
            if (!is_array($field)) {
                continue;
            }
            $checkExists = $this->checkFieldUserDataExists($field["name"], $field["type"]);
            if (!$checkExists) {
                array_push($emptyFields, $field);
            }

        }
        return $emptyFields;
    }

    private function checkFieldUserDataExists(string $name, string $type = null)
    {
        switch ($type) {
            case "file":
                $getFieldMeta = get_user_meta($this->getUser()->ID, "{$name}_attachment_id", true);
                if (isset($getFieldMeta) && $getFieldMeta !== null & $getFieldMeta !== "") {
                    return true;
                }
                break;
            case "data_source":
                $getData = apply_filters("tfr_data_source_data", ["name" => $name], $this->getUser());
                if (is_array($getData) && count($getData) > 0) {
                    return true;
                } elseif (is_object($getData)) {
                    return true;
                }
                break;
            default:
                $getFieldMeta = get_user_meta($this->getUser()->ID, $name, true);
                if (isset($getFieldMeta) && $getFieldMeta !== "") {
                    return true;
                }
                break;
        }
        return false;
    }

    public function buildFieldsGroupArray(array $formFieldGroups, array $progressFieldGroups)
    {
        return array_map(function ($group) use ($progressFieldGroups) {
            $groupName = $group["name"] . self::GROUP_KEY_APPENDIX;
            if (array_key_exists($groupName, $progressFieldGroups)) {
                $group["fields"] = $progressFieldGroups[$groupName];
            }
            if (isset($group["percentage"])) {
                $group["percentage"] = (int)$group["percentage"];
            }
            return $group;
        }, $formFieldGroups);
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

    private function getFormBuilderUserMetaData(array $form = [])
    {
        switch ($form["type"]) {
            case "single":
                return $this->getSingleFormTypeUserMetaData($form);
            case "list":
                return $this->getListFormTypeUserMetaData($form);
            default:
                return false;
        }
    }

    private function getListFormTypeUserMetaData(array $form = [])
    {
        $listFormMetaData = get_user_meta($this->getUser()->ID, $form["id"], true);
        return [
            $form["id"] => (!$listFormMetaData || $listFormMetaData === "") ? [] : $listFormMetaData
        ];
    }

    private function getSingleFormTypeUserMetaData(array $form = [])
    {
        return $this->buildUserMetaDataArray($form["fields"]);
    }

    private function buildUserMetaDataArray(array $data = [])
    {
        $userData = [];
        foreach ($data as $key => $field) {
            if (array_key_exists("form_control", $field)) {
                $userData[$field["name"]] = $this->getFormFieldUserMetaData($field);
            }
        }
        return $userData;
    }

    private function getFormFieldUserMetaData(array $field)
    {
        if (property_exists($this->getUser()->data, $field["name"])) {
            $name = $field["name"];
            return $this->getUser()->data->$name;
        }
        switch ($field["form_control"]) {
            case "file_upload":
            case "image_upload":
                return $this->getUserMetaAttachmentData($field);
            case "select_data_source":
                return apply_filters("tfr_user_meta_select_data_source", $field, $this->getUser());
            case "saved_item_count":
                return $this->getUserSavedItemCount($field);
            default:
                return get_user_meta($this->getUser()->ID, $field["name"], true);
        }
    }

    private function getUserMetaAttachmentData($field)
    {

        $attachmentId = get_user_meta($this->getUser()->ID, $field["name"] . "_attachment_id", true);
        if (wp_attachment_is("image", (int)$attachmentId)) {
            return wp_get_attachment_image_url($attachmentId);
        } else {
            return wp_get_attachment_url($attachmentId);
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

    public function getUserSavedItemCount($field)
    {
        $dbClass = new Tru_Fetcher_Database();
        $where = "user_id=%s";
        $getCount = $dbClass->getCount(
            Tru_Fetcher_Database::SAVED_ITEMS_TABLE_NAME,
            $field["name"],
            $where,
            $this->getUser()->ID
        );
        if (is_array($getCount)) {
            return $getCount[$field["name"]];
        }
        if (is_object($getCount)) {
            $key = $field["name"];
            return $getCount->$key;
        }
        return null;
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
        return true;
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
                $mediaUpload,
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
