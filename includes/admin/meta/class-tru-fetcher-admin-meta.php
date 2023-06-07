<?php
namespace TruFetcher\Includes\Admin\Meta;

use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Item_List;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
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
        add_action( 'init', [$this, 'registerPostMetaFields'] );
        add_action( 'init', [$this, 'myguten_register_post_meta'] );
        add_action( 'add_meta_boxes', [$this, 'addEditorMetaBoxes'] );
        add_action( 'save_post', [$this, 'metaBoxSaveHandler'] );
    }
    public function metaBoxSaveHandler( $post_id ) {
        if ( array_key_exists( 'wporg_field', $_POST ) ) {
            update_post_meta(
                $post_id,
                '_wporg_meta_key',
                $_POST['wporg_field']
            );
        }
    }

    private function buildMetaBoxId( $metaBoxClass ) {
        $config = $metaBoxClass::CONFIG;
        return "{$this->metaBoxIdPrefix}_{$config['id']}";
    }
    public function addEditorMetaBoxes() {
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
    public function renderMetaBox( $post ) {
        foreach ($this->metaBoxes as $metaBoxClass) {
            $config = $metaBoxClass::CONFIG;
            $id = $this->buildMetaBoxId($metaBoxClass);
            $fields = $config['fields'];
            foreach ($fields as $field) {
                $fieldId = "{$this->metaBoxIdPrefix}_post_meta_{$field['id']}";
                echo "<input type='hidden' id='{$fieldId}' name='{$fieldId}' />";
            }
            echo "<div id='{$id}_react'></div>";
        }
        ?>

        <?php
    }
    public static function getMetaFields(string $fieldGroup) {
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
    public static function getMetaFieldConfig() {
        return array_map(function ($fieldGroup) {
            return [
                'name' => $fieldGroup::NAME,
                'fields' => self::getMetaFields($fieldGroup),
            ];
        }, self::$fieldGroups);
    }
    function registerPostMetaFields() {
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

    function myguten_register_post_meta() {
        register_post_meta( 'page', 'meta_fields_page_options_page_type', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ) );
        register_post_meta( 'page', 'meta_fields_page_options_header_override', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
        ) );
        register_post_meta( 'page', 'meta_fields_page_options_header_scripts', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ) );
        register_post_meta( 'page', 'meta_fields_page_options_footer_override', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'boolean',
        ) );
        register_post_meta( 'page', 'meta_fields_page_options_footer_scripts', array(
            'show_in_rest' => true,
            'single' => true,
            'type' => 'string',
        ) );
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
}
