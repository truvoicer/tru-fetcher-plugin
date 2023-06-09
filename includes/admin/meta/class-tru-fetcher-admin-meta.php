<?php

namespace TruFetcher\Includes\Admin\Meta;

use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Item_List;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\Tru_Fetcher_Base;

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
class Tru_Fetcher_Admin_Meta extends Tru_Fetcher_Base
{
    use Tru_Fetcher_Traits_Errors;

    private string $metaBoxIdPrefix = 'trf_mb';
    public static array $fieldGroups = [
        Tru_Fetcher_Meta_Fields_Page_Options::class,
        Tru_Fetcher_Meta_Fields_Post_Options::class,
    ];

    private array $metaBoxes = [
        Tru_Fetcher_Admin_Meta_Box_Single_Item::class,
        Tru_Fetcher_Admin_Meta_Box_Item_List::class,
    ];

    public function init()
    {
        add_action('init', [$this, 'registerPostMetaFields']);
        add_action('init', [$this, 'myguten_register_post_meta']);
        add_action('add_meta_boxes', [$this, 'addEditorMetaBoxes']);
        add_action('save_post', [$this, 'metaBoxSaveHandler']);
    }

    private function buildMetaBoxId($metaBoxClass)
    {
        $config = $metaBoxClass::CONFIG;
        return "{$this->metaBoxIdPrefix}_{$config['id']}";
    }

    private function buildMetaBoxFieldId(array $field)
    {
        return "{$this->metaBoxIdPrefix}_post_meta_{$field['id']}";
    }

    private function buildMetaBoxFieldSaveValue($postField, array $field)
    {
        $emptyPostFieldValues = ['', [], null];
        if (in_array($postField, $emptyPostFieldValues) ) {
            return false;
        }
        switch ($field['type']) {
            case 'array':
                return json_decode(stripslashes($postField));
            default:
                return $postField;
        }
    }

    private function buildMetaBoxFieldValue(array $field, $metaValue)
    {
        switch ($field['type']) {
            case 'array':
                return json_encode($metaValue);
            default:
                return $metaValue;
        }
    }

    private function fetchMetaBoxFieldValue(int $postId, array $field)
    {
        $fieldId = $this->buildMetaBoxFieldId($field);
        $fetchMeta = get_post_meta($postId, $fieldId, true);
        if (!$fetchMeta) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_meta_box_fetch_error',
                    "Invalid post id for meta box field {$fieldId} for post {$postId}"
                )
            );
            return false;
        }
        if (empty($fetchMeta)) {
            return $fetchMeta;
        }
        return $this->buildMetaBoxFieldValue($field, $fetchMeta);
    }

    public function metaBoxSaveHandler($post_id)
    {
        foreach ($this->metaBoxes as $metaBoxClass) {
            $config = $metaBoxClass::CONFIG;
            $fields = $config['fields'];
            foreach ($fields as $field) {
                $fieldId = $this->buildMetaBoxFieldId($field);
                if (array_key_exists($fieldId, $_POST)) {
                    $postField = $_POST[$fieldId];
                    $saveValue = $this->buildMetaBoxFieldSaveValue($postField, $field);
                    if (!$saveValue) {
                        continue;
                    }
                    $update = update_post_meta(
                        $post_id,
                        $fieldId,
                        $saveValue
                    );
                    if (!$update) {
                        $this->addError(
                            new \WP_Error(
                                'tru_fetcher_meta_box_save_error',
                                "Failed to save meta box field {$fieldId} for post {$post_id}"
                            )
                        );
                    }
                }
            }
        }
    }

    public function addEditorMetaBoxes()
    {
        foreach ($this->metaBoxes as $metaBoxClass) {
            $config = $metaBoxClass::CONFIG;
            $id = $this->buildMetaBoxId($metaBoxClass);
            $title = $config['title'];
            foreach ($config['post_types'] as $postType) {
                add_meta_box(
                    $id,
                    $title,
                    [$this, 'renderMetaBox'],
                    $postType
                );
            }
        }
    }

    public function getMetaboxConfig(?array $postTypes = []) {
        $data = [];
        foreach ($this->metaBoxes as $metaBoxClass) {
            $config = $metaBoxClass::CONFIG;
            if (count($postTypes) && !count(array_intersect($postTypes, $config['post_types']))) {
                continue;
            }
            $config['fields'] = array_map(function ($field) {
                $field['field_name'] = $this->buildMetaBoxFieldId($field);
                return $field;
            }, $config['fields']);
            $data[] = $config;
        }
        return $data;
    }

    public function getMetaboxPostTypes() {
        $postTypes = [];
        foreach ($this->metaBoxes as $metaBoxClass) {
            $config = $metaBoxClass::CONFIG;
            foreach ($config['post_types'] as $postType) {
                $postTypes[] = $postType;
            }
        }
        return $postTypes;
    }

    public function buildInputArgs(array $inputArgs)
    {
        $args = [];
        foreach ($inputArgs as $key => $value) {
            $args[] = "{$key}='{$value}'";
        }
        return implode(' ', $args);
    }

    public function renderMetaBox($post)
    {
        foreach ($this->metaBoxes as $metaBoxClass) {
            $config = $metaBoxClass::CONFIG;
            $id = $this->buildMetaBoxId($metaBoxClass);
            $fields = $config['fields'];
            foreach ($fields as $field) {
                $fieldId = $this->buildMetaBoxFieldId($field);
                $metaValue = $this->fetchMetaBoxFieldValue($post->ID, $field);
                $inputArgs = [
                    'id' => $fieldId,
                    'name' => $fieldId,
                    'type' => 'hidden',
                ];
                if ($metaValue) {
                    $inputArgs['value'] = $metaValue;
                }
                echo "<input {$this->buildInputArgs($inputArgs)} />";
            }
            echo "<div id='{$id}_react'></div>";
        }
    }

    public static function getMetaFields(string $fieldGroup)
    {
        $name = $fieldGroup::NAME;
        return array_map(function ($field) use ($name) {
            return [
                'post_type' => $field['post_type'],
                'meta_key' => sprintf(
                    '%s_%s_%s',
                    TRU_FETCHER_PLUGIN_NAME_ACRONYM,
                    $name,
                    $field['meta_key']
                ),
                'args' => $field['args'],
            ];
        }, $fieldGroup::FIELDS);
    }

    public static function getMetaFieldConfig()
    {
        $data = [];
        foreach (self::$fieldGroups as $fieldGroup) {
            $data[] = [
                'name' => $fieldGroup::NAME,
                'fields' => self::getMetaFields($fieldGroup),
            ];
        }
        return $data;
    }

    function registerPostMetaFields()
    {
        foreach (self::getMetaFieldConfig() as $fieldGroup) {
            $fields = $fieldGroup['fields'];
            foreach ($fields as $field) {
                register_post_meta(
                    $field['post_type'],
                    $field['meta_key'],
                    $field['args']
                );
            }
        }
    }

    function myguten_register_post_meta()
    {
        register_post_meta('page', 'meta_fields_page_options_page_type', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
        register_post_meta('page', 'meta_fields_page_options_header_override', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
        ));
        register_post_meta('page', 'meta_fields_page_options_header_scripts', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
        register_post_meta('page', 'meta_fields_page_options_footer_override', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
        ));
        register_post_meta('page', 'meta_fields_page_options_footer_scripts', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ));
    }

    public function replacePostTypes($fields)
    {
        foreach ($fields as $key => $field) {
            if (is_array($field)) {
                $fields[$key] = $this->replacePostTypes($field);
            } else if (!$field instanceof WP_Post) {
                $fields[$key] = $field;
            } else if (array_key_exists($field->post_type, self::REPLACEABLE_POST_TYPES)) {
                $getFields = \get_fields_clone($field->ID);
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

    /**
     * @return array
     */
    public function getMetaBoxes(): array
    {
        return $this->metaBoxes;
    }
}
