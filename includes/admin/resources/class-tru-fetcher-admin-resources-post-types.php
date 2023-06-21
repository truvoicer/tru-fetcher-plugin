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
class Tru_Fetcher_Admin_Resources_Post_Types {
    public const FETCHER_ITEMS_LIST_PT = 'fetcher_items_lists';
    public const FETCHER_SINGLE_ITEM_PT = 'fetcher_single_item';
    public const FETCHER_FILTER_LISTS_PT = 'filter_lists';
    public const FETCHER_CATEGORY_TEMPLATES_PT = 'category_templates';
    public const FETCHER_ITEM_VIEW_TEMPLATES_PT = 'item_view_templates';
    public const FETCHER_POST_TEMPLATES_PT = 'post_templates';

    public static function getPostTypeData(string $postType)
    {
        return get_posts([
            'post_type' => $postType,
            'posts_per_page' => -1,
            'post_status' => 'any',
        ]);
    }
}
