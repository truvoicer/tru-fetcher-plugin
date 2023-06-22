<?php

namespace TruFetcher\Includes\Admin\Resources;

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
class Tru_Fetcher_Admin_Resources_Taxonomies {

    public const LISTINGS_CATEGORIES_TAXONOMY = 'listings_categories';
    public const CATEGORY_TAXONOMY = 'category';

    public static function getTerms(string $taxonomy) {
        $getTerms = get_terms([
            'taxonomy' => $taxonomy,
            "hide_empty" => false
        ]);
        if (is_wp_error($getTerms)) {
            return $getTerms;
        }
        return $getTerms;
    }
}
