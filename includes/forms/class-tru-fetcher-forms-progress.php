<?php
namespace TruFetcher\Includes\Forms;

use TruFetcher\Includes\Api\Providers\Tru_Fetcher_Api_Providers_Hubspot;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Forms_Response;
use TruFetcher\Includes\Database\Tru_Fetcher_Database;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_User;
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
class Tru_Fetcher_Forms_Progress
{
    use Tru_Fetcher_Traits_Errors, Tru_Fetcher_Traits_User;

    const GROUP_KEY_APPENDIX = "_group";

    private ?int $overallProgressPercentage = null;
    private ?array $groups = null;


    public function __construct()
    {
    }

    public function getFormsProgressData(WP_REST_Request $request)
    {
        $formFieldGroups = $request->get_param('form_field_groups');
        if (empty($formFieldGroups)) {
            return $this->showError("invalid_request", "(form_field_groups) fields does not exist in request");
        }
        $progressFieldGroups = apply_filters("tfr_form_progress_field_groups", $formFieldGroups, $this->getUser());
        $buildFieldsGroupArray = $this->buildFieldsGroupArray($request["form_field_groups"], $progressFieldGroups);

        $this->groups = $this->buildFormsProgressData($buildFieldsGroupArray);

        $this->overallProgressPercentage = $this->calculateOverallProgressPercent($this->groups);

        return $this;
    }

    private function buildFormsProgressData(array $fieldGroups)
    {
        return array_map(function ($group) {
            $fields = (!empty($group["fields"]) && is_array($group["fields"]))
                ? $group["fields"]
                : [];
            $emptyFields = $this->buildEmptyFieldsArray(
                $fields
            );
            $group["empty_fields"] = $emptyFields;
            $group["fields_complete_percent"] = $this->calculateCompletedFieldsPercent(
                count($group["empty_fields"]),
                count($fields)
            );
            $group["fields_incomplete_percent"] = $this->calculateIncompleteFieldsPercent(
                count($group["empty_fields"]),
                count($fields)
            );
            $group["group_complete_percent"] = $this->calculateGroupCompletePercent(
                count($group["empty_fields"]),
                count($fields),
                $group["percentage"]
            );
            $group["group_incomplete_percent"] = $this->calculateGroupIncompletePercent(
                count($group["empty_fields"]),
                count($fields),
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
        if ($totalFields === 0) {
            return 0;
        }
        $completed = ($totalFields - $emptyCount) / $totalFields;
        return $completed * 100;
    }

    private function calculateIncompleteFieldsPercent(int $emptyCount, int $totalFields)
    {
        if ($totalFields === 0) {
            return 0;
        }
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

    /**
     * @return int|null
     */
    public function getOverallProgressPercentage(): ?int
    {
        return $this->overallProgressPercentage;
    }

    /**
     * @return array|null
     */
    public function getGroups(): ?array
    {
        return $this->groups;
    }

}
