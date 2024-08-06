<?php
namespace TruFetcher\Includes\Forms;

use TruFetcher\Includes\Api\Providers\Tru_Fetcher_Api_Providers_Hubspot;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Forms_Response;
use TruFetcher\Includes\Database\Tru_Fetcher_Database;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\Tru_Fetcher_Filters;
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
class Tru_Fetcher_Api_Form_Handler
{
    use Tru_Fetcher_Traits_Errors;

    const REQUEST_TEXT_FIELDS = [
        "user_email", "display_name", "first_name", "surname", "telephone", "town", "country"
    ];
    const REQUEST_FORM_ARRAY_FIELDS = [];

    const GROUP_KEY_APPENDIX = "_group";

    private \WP_User $user;

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
        $endpointProviders = $formData["external_providers"];
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

    public function fetchUserMetaData(WP_REST_Request $request)
    {
        $data = $request->get_params();
        return $this->getFormBuilderUserMetaData($data["form"]);
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
                return apply_filters(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_USER_META_SELECT_DATA_SOURCE, $field, $this->getUser());
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

    public function buildSelectList($entity, $idKey, $valueKey, $labelKey, $data)
    {
        return array_map(function ($item) use ($entity, $idKey, $valueKey, $labelKey) {
            return [
                "entity" => $entity,
                "value" => $item[$valueKey],
                "label" => $item[$labelKey],
                "id" => $item[$idKey]
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
                $this->addError($mediaUpload);
                $errors[] = true;
                continue;
            }
            $metaKey = "{$key}_attachment_id";
            $updateMeta = update_user_meta(
                $user->ID,
                $metaKey,
                $mediaUpload,
            );
            if (!$updateMeta) {
                $this->addError(
                    new \WP_Error(
                        "user_meta_update_error",
                        "Error updating user profile | {$metaKey}",
                        ["value" => $mediaUpload]
                    )
                );
                $errors[] = true;
                continue;
            }
            $attachments[] = [
                "attachment_id" => $mediaUpload,
                "file_name" => $key
            ];
        }
        return [
            "hasErrors" => (count($errors) > 0),
            "attachments" => $attachments
        ];
    }

    public function saveUserProfileMeta(WP_User $user, array $profileData = [])
    {
        $profileData = array_filter($profileData, function ($key) {
            return in_array($key, [...self::REQUEST_TEXT_FIELDS, ...self::REQUEST_FORM_ARRAY_FIELDS]);
        }, ARRAY_FILTER_USE_KEY);
        $errors = [];
        foreach ($profileData as $key => $value) {
            $updateUserMeta = update_user_meta(
                $user->ID,
                $key,
                $value
            );
            if (!$updateUserMeta) {
                $this->addError(
                    new \WP_Error(
                        "user_meta_update_error",
                        "Error updating user profile | {$key}",
                        ["value" => $value]
                    )
                );
                $errors[] = $key;
            }
        }
        $applyFilter = apply_filters(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_USER_PROFILE_SAVE, $user, $profileData);
        if ($applyFilter === true) {
            return count($errors) === 0;
        }
        if (!is_array($applyFilter)) {
            $this->addError(
                new \WP_Error(
                    "user_meta_update_error",
                    sprintf(
                        'Invalid response format for filter: %s | expected WP_Error[] but got %s',
                        Tru_Fetcher_Filters::TRU_FETCHER_FILTER_USER_PROFILE_SAVE,
                        gettype($applyFilter)
                    ),
                    $profileData
                )
            );
            return false;
        }
        $validate = array_filter($applyFilter, function ($error) {
            return !is_wp_error($error);
        });
        if (count($validate) > 0) {
            $this->addError(
                new \WP_Error(
                    "user_meta_update_error",
                    sprintf(
                        'Some items in filter response are not WP_Error[]: filter: %s | %s',
                        Tru_Fetcher_Filters::TRU_FETCHER_FILTER_USER_PROFILE_SAVE,
                        json_encode($validate)
                    ),
                    $profileData
                )
            );
        }
        $this->setErrors(array_merge($errors, $applyFilter));
        $errors = array_merge($errors, $applyFilter);
        return count($errors) === 0;
    }


    protected function showError( $code, $message ) {
        return new \WP_Error( $code,
            esc_html__( $message, 'my-text-domain' ),
            array( 'status' => 404 ) );
    }


    /**
     * @return \WP_User
     */
    public function getUser(): \WP_User
    {
        return $this->user;
    }

    /**
     * @param \WP_User $user
     */
    public function setUser(\WP_User $user): void
    {
        $this->user = $user;
    }

}
