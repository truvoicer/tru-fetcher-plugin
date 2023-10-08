<?php

namespace TruFetcher\Includes\Admin\Meta\PostMeta\Gutenberg\MetaFields;

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
class Tru_Fetcher_Meta_Fields_Base
{
    protected string $name;
    protected string $postType;
    protected array $fields = [];
    private static string $gutenbergMetaIdPrefix = 'trf_gut_pmf';

    public static function buildGutenbergMetaFieldId(array $field)
    {
        return sprintf("%s_%s",
            self::$gutenbergMetaIdPrefix,
            $field['meta_key']
        );
    }

    public function getField(string $meta_key): array|null
    {
        foreach ($this->fields as $field) {
            if ($field['meta_key'] === $meta_key) {
                $field['meta_key'] = $this->buildGutenbergMetaFieldId($field);
                return $field;
            }
        }
        return null;
    }

    public function renderPost(\WP_Post $post ) {
        $post->{$this->name} = [];
        foreach ($this->fields as $field) {
            $metaKey = self::buildGutenbergMetaFieldId($field);
            $single = false;
            if (isset($field['args']['single'])) {
                $single = (bool)$field['args']['single'];
            }
            $metaValue = get_post_meta($post->ID, $metaKey, $single);
            if (empty($metaValue) && isset($field['default'])) {
                $metaValue = $field['default'];
            }
            $post->{$this->name}[$metaKey] = $metaValue;

        }
        return $post;
    }
    public function buildPostMetaFieldsData(\WP_Post $post ) {
        $options = [];
        foreach ($this->fields as $field) {
            $metaKey = self::buildGutenbergMetaFieldId($field);
            $single = false;
            if (isset($field['args']['single'])) {
                $single = (bool)$field['args']['single'];
            }
            $metaValue = get_post_meta($post->ID, $metaKey, $single);
            if (empty($metaValue) && isset($field['default'])) {
                $metaValue = $field['default'];
            }
            $options[$metaKey] = $metaValue;

        }
        return $options;
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return array
     */
    public function getFields(): array
    {
        return $this->fields;
    }

    public function getPostType(): string
    {
        return $this->postType;
    }

}
