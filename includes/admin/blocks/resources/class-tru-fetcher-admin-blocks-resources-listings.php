<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Post_Types;
use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Taxonomies;
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
class Tru_Fetcher_Admin_Blocks_Resources_Listings
{
    public const CONFIG = [
        'id' => 'listings-block',
        'name' => 'tru-fetcher/listings-block',
        'title' => 'Tf Listings Block',
        'post_types' => [
            [
                'name' => Tru_Fetcher_Admin_Resources_Post_Types::FETCHER_ITEMS_LIST_PT
            ],
            [
                'name' => Tru_Fetcher_Admin_Resources_Post_Types::FETCHER_SINGLE_ITEM_PT
            ],
        ],
        'taxonomies' => [
            ['name' => Tru_Fetcher_Admin_Resources_Taxonomies::LISTINGS_CATEGORIES_TAXONOMY],
        ],
        'attributes' => [
            [
                'id' => 'source',
                'type' => 'string',
                'default' => 'api',
            ],
            [
                'id' => 'listing_block_type',
                'type' => 'string',
                'default' => 'search',
            ],
            [
                'id' => 'listings_category',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'api_listings_category',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'select_providers',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'providers_list',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'heading',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'item_view_display',
                'type' => 'string',
                'default' => 'page',
            ],
            [
                'id' => 'load_more_type',
                'type' => 'string',
                'default' => 'infinite_scroll',
            ],
            [
                'id' => 'show_filters_toggle',
                'type' => 'boolean',
                'default' => true,
            ],
            [
                'id' => 'search_limit',
                'type' => 'integer',
                'default' => 20,
            ],
            [
                'id' => 'initial_load',
                'type' => 'string',
                'default' => 'search',
            ],
            [
                'id' => 'list_start',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'list_end',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'custom_position',
                'type' => 'boolean',
                'default' => false,
            ],
        ]
    ];

    public function renderBlock( $block_attributes, $content ) {
        var_dump($block_attributes);
        return '<div class="tru-fetcher-listings-block">sfdsfdsfdsfd</div>';
    }
}
