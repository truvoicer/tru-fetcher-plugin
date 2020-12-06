<?php

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
class Tru_Fetcher_Blocks extends Tru_Fetcher_Base
{

    const REPLACEABLE_POST_TYPES = [
        "fetcher_items_lists" => "items_data",
        "filter_lists" => "list_items",
        "fetcher_single_item" => ["data_keys", "custom_item_options"]
    ];

    public function blocks_init()
    {
        $this->registerBlocks();
    }

    public function registerBlocks()
    {
        Tru_Fetcher::directoryIncludes('includes/blocks/register-blocks', 'acf-register.php');
    }

    public function getBlockData($block)
    {
        acf_setup_meta($block['data'], $block['id'], true);
        $fields = get_fields();
        if (!$fields) {
            return [];
        }
        return $this->replacePostTypes($fields);
    }

    public function getBlockDataJson($data)
    {
        $dataJson = json_encode($data);
        if (!$dataJson) {
            return false;
        }
        return htmlentities($dataJson, ENT_QUOTES, 'UTF-8');
    }

    public function replacePostTypes($fields)
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $fields[$key] = $this->replacePostTypes($field);
            } else if (!$field instanceof WP_Post) {
                $fields[$key] = $field;
            } else if (array_key_exists($field->post_type, self::REPLACEABLE_POST_TYPES)) {
                $getFields = get_fields($field->ID);
                $fieldNames = self::REPLACEABLE_POST_TYPES[$field->post_type];

                if (is_array($fieldNames)) {
                    $fields[$key] = [];
                    $getSubPostTypeArray = $this->replaceSubPostTypeArray($fieldNames, $getFields);
                    if (!$getSubPostTypeArray) {
                        $fields[$key] = [$field];
                        continue;
                    }
                    $fields[$key]["data"] = $getSubPostTypeArray;
                    $fields[$key]["post_type"] = $field;

                } else if ($getFields && isset($getFields[self::REPLACEABLE_POST_TYPES[$field->post_type]])) {
                        $fields[$key] = [];
                        $fields[$key]["post_type"] = $field;
                        $fields[$key]["data"] = $this->replacePostTypes($getFields[$fieldNames]);
                } else {
                    $fields[$key] = $field;
                }

            }

        }
        return $fields;
    }

    private function replaceSubPostTypeArray($fieldNames, $getFields)
    {
        if (!$getFields) {
            return false;
        }
        $array = [];
        foreach ($fieldNames as $fieldName) {
            if ($getFields && isset($getFields[$fieldName])) {
                $array = array_merge($array, $this->replacePostTypes($getFields[$fieldName]));
            }
        }
        return $array;
    }
}
