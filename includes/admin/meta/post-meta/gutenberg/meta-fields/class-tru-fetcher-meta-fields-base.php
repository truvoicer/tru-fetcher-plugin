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
    protected array $fields = [];
    private static string $gutenbergMetaIdPrefix = 'tf_mg';

    public static function buildGutenbergMetaFieldId(array $field)
    {
        return sprintf("%s_post_meta_%s",
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

}
