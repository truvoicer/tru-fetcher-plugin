<?php

namespace TruFetcher\Includes\Admin\Meta\Box;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Filter_List;
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
class Tru_Fetcher_Admin_Meta_Box_Base extends Tru_Fetcher_Base
{
    public const META_BOX_ID_PREFIX = 'trf_mb';

    protected array $config;

    public function getFields() {
        if (empty($this->config)) {
            return false;
        }
        if (empty($this->config['fields'])) {
            return false;
        }
        if (!is_array($this->config['fields'])) {
            return false;
        }
        return array_map(function ($field) {
            $field['field_name'] = self::buildMetaBoxFieldId($field);
            return $field;
        }, $this->config['fields']);
    }
    public function buildMetaBoxFieldData(\WP_Post $post) {
        $configFields = $this->getFields();
        if (!$configFields) {
            return false;
        }
        $fields = [];
        foreach ($configFields as $field) {
            $fields[$field['id']] = get_post_meta($post->ID, $field['field_name'], true);
        }
        return $fields;
    }

    public static function buildMetaBoxId($metaBoxClass)
    {
        $config = $metaBoxClass::CONFIG;
        $metaBoxIdPrefix = self::META_BOX_ID_PREFIX;
        return "{$metaBoxIdPrefix}_{$config['id']}";
    }

    public static function buildMetaBoxFieldId(array $field)
    {
        $metaBoxIdPrefix = self::META_BOX_ID_PREFIX;
        return "{$metaBoxIdPrefix}_post_meta_{$field['id']}";
    }
}
