<?php
namespace TruFetcher\Includes\Admin\Editor;

use TruFetcher\Includes\Admin\Editor\MetaFields\Tru_Fetcher_Meta_Fields_Page_Options;
use TruFetcher\Includes\Admin\Editor\MetaFields\Tru_Fetcher_Meta_Fields_Post_Options;
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

    public function init()
    {
        add_action( 'init', [$this, 'registerPostMetaFields'] );
        add_action( 'add_meta_boxes', [$this, 'addEditorMetaBoxes'] );
    }

    public function addEditorMetaBoxes() {
        add_meta_box(
            'trf_mb_ft_single_comparison',                 // Unique ID
            'Custom Meta Box Title',      // Box title
            [$this, 'wporg_custom_box_html'],  // Content callback, must be of type callable
            'ft_single_comparison'                            // Post type
        );
    }
    public function wporg_custom_box_html( $post ) {
        ?>
        <div id="trf_single_comparison">
        </div>
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
