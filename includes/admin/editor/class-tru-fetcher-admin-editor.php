<?php
namespace TruFetcher\Includes\Admin\Editor;

use TruFetcher\Includes\Admin\Editor\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Admin\Editor\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
use TruFetcher\Includes\Tru_Fetcher;
use TruFetcher\Includes\Tru_Fetcher_Base;
use WP_Post;

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
class Tru_Fetcher_Admin_Editor extends Tru_Fetcher_Base
{
    public static array $fieldGroups = [
        Tru_Fetcher_Meta_Fields_Page_Options::class,
        Tru_Fetcher_Meta_Fields_Post_Options::class,
    ];

    private array $singleItemPostTypes = [
        Tru_Fetcher_Admin_Post_Types::FETCHER_SINGLE_COMPARISON_PT,
    ];

    public function init()
    {
        add_action( 'init', [$this, 'registerPostMetaFields'] );
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
    public function addEditorMetaBoxes() {
        foreach ($this->singleItemPostTypes as $postType) {
            add_meta_box(
                'trf_mb_single_item',
                'Custom Meta Box Title',
                [$this, 'singleItemMetaBox'],
                $postType
            );
        }
    }
    public function singleItemMetaBox( $post ) {
        ?>
        <input type="hidden" name="trf_post_meta_type">
        <input type="hidden" name="trf_post_meta_data_keys">
        <input type="hidden" name="trf_post_meta_custom">
        <div id="trf_single_comparison"></div>
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
}
