<?php

namespace TruFetcher\Includes\Forms;

use TruFetcher\Includes\Api\Providers\Tru_Fetcher_Api_Providers_Hubspot;
use TruFetcher\Includes\Api\Response\Tru_Fetcher_Api_Forms_Response;
use TruFetcher\Includes\Database\Tru_Fetcher_Database;
use TruFetcher\Includes\Forms\ProgressGroups\Tru_Fetcher_Progress_Field_Groups;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_User;
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
class Tru_Fetcher_Forms_Progress
{
    use Tru_Fetcher_Traits_Errors, Tru_Fetcher_Traits_User;

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

        $progressFieldGroups = $this->getFormFieldGroupsArray($formFieldGroups);
        $buildFieldsGroupArray = $this->buildFieldsGroupArray($request["form_field_groups"], $progressFieldGroups);

        $this->groups = $this->buildFormsProgressData($buildFieldsGroupArray);

        $this->overallProgressPercentage = $this->calculateOverallProgressPercent($this->groups);

        return $this;
    }

    public function getFormFieldGroupsArray(?array $formFieldGroups = [])
    {
        $classes = [
            Tru_Fetcher_Progress_Field_Groups::class,
            ...$this->getFilteredFieldGroupClasses($formFieldGroups)
        ];
        $progressFieldGroups = [];
        foreach ($classes as $class) {
            $instance = new $class();
            if (!$instance instanceof Tru_Fetcher_Progress_Field_Groups) {
                continue;
            }
            if (!method_exists($instance, "getFieldGroups")) {
                continue;
            }
            $progressFieldGroups = array_merge($progressFieldGroups, $instance->getFieldGroups(
                array_column($formFieldGroups, "name")
            ));
        }
        return $progressFieldGroups;
    }

    private function getFilteredFieldGroupClasses(?array $formFieldGroups = [])
    {
        if (!has_filter(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_FORM_PROGRESS_FIELD_GROUPS)) {
            return [];
        }
        $progressFieldGroups = apply_filters(
            Tru_Fetcher_Filters::TRU_FETCHER_FILTER_FORM_PROGRESS_FIELD_GROUPS,
            $this->getUser()
        );
        return array_filter($progressFieldGroups, function ($progressFieldGroup) {
            return class_exists($progressFieldGroup) && is_subclass_of($progressFieldGroup, Tru_Fetcher_Progress_Field_Groups::class);
        });
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
                $getData = apply_filters(Tru_Fetcher_Filters::TRU_FETCHER_FILTER_DATA_SOURCE_DATA, ["name" => $name], $this->getUser());
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
            $groupName = $group["name"];
            if (array_key_exists($groupName, $progressFieldGroups)) {
                $group["fields"] = $progressFieldGroups[$groupName];
            }
            if (isset($group["percentage"])) {
                $group["percentage"] = (int)$group["percentage"];
            }
            return $group;
        }, $formFieldGroups);
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
