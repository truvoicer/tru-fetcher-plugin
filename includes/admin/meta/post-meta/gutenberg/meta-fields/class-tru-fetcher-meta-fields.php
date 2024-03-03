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
class Tru_Fetcher_Meta_Fields
{
    public const META_FIELDS = [
        Tru_Fetcher_Meta_Fields_Page_Options::class,
        Tru_Fetcher_Meta_Fields_Post_Options::class,
    ];
    private static string $gutenbergMetaIdPrefix = 'trf_gut_pmf';

    public static function buildGutenbergMetaFieldId(array $field)
    {
        return sprintf("%s_%s",
            self::$gutenbergMetaIdPrefix,
            $field['meta_key']
        );
    }
    public static function getMetaFieldIdByMetaKey(string $meta_key): string|null
    {
        foreach (self::META_FIELDS as $metaField) {
            $metaField = new $metaField();
            $field = $metaField->getField($meta_key);
            if (empty($field['meta_key'])) {
                continue;
            }
            return self::buildGutenbergMetaFieldId($field);
        }
        return null;
    }
}
