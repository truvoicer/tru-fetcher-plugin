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
class Tru_Fetcher_Admin_Meta_Box_Base
{
    public const META_BOX_ID_PREFIX = 'trf_mb';
    protected string $id;
    protected string $title;
    protected array $config;

    public function __construct()
    {
    }

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
            $field['field_name'] = self::buildMetaBoxFieldId($this->getId(), $field['id']);
            return $field;
        }, $this->config['fields']);
    }
    public function renderPost(\WP_Post $post) {
        $configFields = $this->getFields();
        if (!$configFields) {
            return false;
        }
        $post->{$this->id} = [];
        foreach ($configFields as $field) {
            $post->{$this->id}[$field['id']] = get_post_meta($post->ID, $field['field_name'], true);
        }
        return $post;
    }

    public function buildMetaBoxId()
    {
        $config = $this->getConfig();
        $metaBoxIdPrefix = self::META_BOX_ID_PREFIX;
        return "{$metaBoxIdPrefix}_{$config['id']}";
    }

    public static function buildMetaBoxFieldId(string $id, string $fieldId)
    {
        $metaBoxIdPrefix = self::META_BOX_ID_PREFIX;
        return "{$metaBoxIdPrefix}_{$id}_field_{$fieldId}";
    }

    public function buildPostApiKeys(\WP_Post $post) {
        if (empty($post->{$this->id}['data_keys']) || !is_array($post->{$this->id}['data_keys'])) {
            return $post;
        }

        $dataKeys = [];
        foreach ($post->{$this->id}['data_keys'] as $dataKey) {
            $dataKeys[$dataKey->data_item_key] = $dataKey->data_item_value;
        }
        $post->{$this->id}['data_keys'] = $dataKeys;
        return $post;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }


}
