<?php

namespace TruFetcher\Includes\Admin\Meta;

use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Api_Data_Keys;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Base;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Filter_Lists;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Item_List;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Base;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
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

    public static array $fieldGroups = [
        Tru_Fetcher_Meta_Fields_Page_Options::class,
        Tru_Fetcher_Meta_Fields_Post_Options::class,
    ];

    public const META_BOXES = [
        Tru_Fetcher_Admin_Meta_Box_Single_Item::class,
        Tru_Fetcher_Admin_Meta_Box_Item_List::class,
        Tru_Fetcher_Admin_Meta_Box_Filter_Lists::class,
//        Tru_Fetcher_Admin_Meta_Box_Api_Data_Keys::class
    ];

    public function init()
    {
        add_action('init', [$this, 'registerPostMetaFields']);
        add_action('init', [$this, 'myguten_register_post_meta']);
        add_action('add_meta_boxes', [$this, 'addEditorMetaBoxes']);
        add_action('save_post', [$this, 'metaBoxSaveHandler']);
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

    private function fetchMetaBoxFieldValue(int $postId, string $id, array $field)
    {
        $fieldId = Tru_Fetcher_Admin_Meta_Box_Base::buildMetaBoxFieldId($id, $field['id']);
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
        foreach (self::META_BOXES as $metaBoxClass) {
            $metaBoxInstance = new $metaBoxClass();
            $config = $metaBoxInstance->getConfig();
            $fields = $config['fields'];
            foreach ($fields as $field) {
                $fieldId = Tru_Fetcher_Admin_Meta_Box_Base::buildMetaBoxFieldId($metaBoxInstance->getId(), $field['id']);
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

    public function findMetaBoxInstanceById(string $id)
    {
        foreach (self::META_BOXES as $metaBoxClass) {
            $metaBoxInstance = new $metaBoxClass();
            $config = $metaBoxInstance->getConfig();
            $postTypes = $config['post_types'];
            $findPostTypeIndex = array_search($id, array_column($postTypes, 'name'));
            if ($findPostTypeIndex === false) {
                continue;
            }
            $postType = $postTypes[$findPostTypeIndex];
            if (!empty($postType['show'])) {
                return $metaBoxInstance;
            }
        }
        return false;
    }
    public function addEditorMetaBoxes()
    {
        $currentScreen = get_current_screen();
        $findMetaBoxInstance = $this->findMetaBoxInstanceById($currentScreen->id);
        if (!$findMetaBoxInstance) {
            return;
        }
        $config = $findMetaBoxInstance->getConfig();
        $id = $findMetaBoxInstance->buildMetaBoxId();
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

    public function getMetaboxClasses(?array $postTypes = []) {
        $data = [];
        foreach (self::META_BOXES as $metaBoxClass) {
            $metaBoxInstance = new $metaBoxClass();
            $config = $metaBoxInstance->getConfig();
            if (count($postTypes) && !count(array_intersect($postTypes, array_map(function ($postType) {
                    return $postType['name'];
                }, $config['post_types'])))) {
                continue;
            }
            $data[] = $metaBoxClass;
        }
        return $data;
    }
    public function getMetaboxConfig(?array $postTypes = []) {
        $postTypesManager = new Tru_Fetcher_Post_Types();
        $data = [];
        foreach (self::META_BOXES as $metaBoxClass) {
            $metaBoxInstance = new $metaBoxClass();
            $config = $metaBoxInstance->getConfig();
            if (count($postTypes) && !count(array_intersect($postTypes, array_map(function ($postType) {
                    return $postType['name'];
                }, $config['post_types'])))) {
                continue;
            }
            $config['post_types'] = array_map(function (array $postType) use($postTypesManager) {
                $postTypeClass = $postTypesManager->findPostTypeByName($postType['name']);
                $postTypeInstance = new $postTypeClass();
                return $postTypeInstance->getConfig();
            }, $config['post_types']);
            $config['fields'] = array_map(function ($field) use ($metaBoxInstance) {
                $field['field_name'] = Tru_Fetcher_Admin_Meta_Box_Base::buildMetaBoxFieldId($metaBoxInstance->getId(), $field['id']);
                return $field;
            }, $config['fields']);
            $data[] = $config;
        }
        return $data;
    }

    public function getMetaboxPostTypes() {
        $postTypes = [];
        foreach (self::META_BOXES as $metaBoxClass) {
            $metaBoxInstance = new $metaBoxClass();
            $config = $metaBoxInstance->getConfig();
            foreach ($config['post_types'] as $postType) {
                $postTypes[] = $postType['name'];
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
        foreach (self::META_BOXES as $metaBoxClass) {
            $metaBoxInstance = new $metaBoxClass();
            $config = $metaBoxInstance->getConfig();
            $id = $metaBoxInstance->buildMetaBoxId();
            $fields = $config['fields'];
            foreach ($fields as $field) {
                $fieldId = Tru_Fetcher_Admin_Meta_Box_Base::buildMetaBoxFieldId($metaBoxInstance->getId(), $field['id']);
                $metaValue = $this->fetchMetaBoxFieldValue($post->ID, $metaBoxInstance->getId(), $field);
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

    public function getMetaFields(Tru_Fetcher_Meta_Fields_Base $fieldGroupClass)
    {
        return array_map(function ($field) use ($fieldGroupClass) {
            $id = Tru_Fetcher_Meta_Fields::buildGutenbergMetaFieldId($field);
            return [
                'post_type' => $field['post_type'],
                'meta_key' => $id,
                'args' => $field['args'],
            ];
        }, $fieldGroupClass->getFields());
    }

    public function getMetaFieldConfig()
    {
        $data = [];
        foreach (self::$fieldGroups as $fieldGroup) {
            $fieldGroupClass = new $fieldGroup();
            $data[] = [
                'name' => $fieldGroupClass->getName(),
                'fields' => $this->getMetaFields($fieldGroupClass),
            ];
        }
        return $data;
    }

    function registerPostMetaFields()
    {
        foreach ($this->getMetaFieldConfig() as $fieldGroup) {
            $fields = $fieldGroup['fields'];
            foreach ($fields as $field) {
                if (!isset($field['post_type'])) {
                    continue;
                }
                if (is_array($field['post_type'])) {
                    foreach ($field['post_type'] as $postType) {
                        register_post_meta(
                            $postType,
                            $field['meta_key'],
                            $field['args']
                        );
                    }
                    continue;
                }
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
        return self::META_BOXES;
    }

}
