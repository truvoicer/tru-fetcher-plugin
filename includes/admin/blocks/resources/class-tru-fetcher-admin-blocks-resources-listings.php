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
class Tru_Fetcher_Admin_Blocks_Resources_Listings extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'listings_block';
    public const BLOCK_NAME = 'tru-fetcher/listings-block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
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
            'source' => [
                'id' => 'source',
                'type' => 'string',
                'default' => 'api',
            ],
            'listing_block_type' => [
                'id' => 'listing_block_type',
                'type' => 'string',
                'default' => 'search',
            ],
            'listings_category' => [
                'id' => 'listings_category',
                'type' => 'string',
                'default' => '',
            ],
            'api_listings_category' => [
                'id' => 'api_listings_category',
                'type' => 'string',
                'default' => '',
            ],
            'select_providers' => [
                'id' => 'select_providers',
                'type' => 'boolean',
                'default' => false,
            ],
            'providers_list' => [
                'id' => 'providers_list',
                'type' => 'array',
                'default' => [],
            ],
            'heading' => [
                'id' => 'heading',
                'type' => 'string',
                'default' => '',
            ],
            'item_view_display' => [
                'id' => 'item_view_display',
                'type' => 'string',
                'default' => 'page',
            ],
            'load_more_type' => [
                'id' => 'load_more_type',
                'type' => 'string',
                'default' => 'infinite_scroll',
            ],
            'show_filters_toggle' => [
                'id' => 'show_filters_toggle',
                'type' => 'boolean',
                'default' => true,
            ],
            'search_limit' => [
                'id' => 'search_limit',
                'type' => 'integer',
                'default' => 20,
            ],
            'initial_load' => [
                'id' => 'initial_load',
                'type' => 'string',
                'default' => 'search',
            ],
            'initial_load_search_params' => [
                'id' => 'initial_load_search_params',
                'type' => 'array',
                'default' => [],
            ],
            'initial_load_request_params' => [
                'id' => 'initial_load_request_params',
                'type' => 'array',
                'default' => [],
            ],
            'initial_load_request_name' => [
                'id' => 'initial_load_request_name',
                'type' => 'string',
            ],
            'initial_load_request_limit' => [
                'id' => 'initial_load_request_limit',
                'type' => 'integer',
            ],
            'list_start' => [
                'id' => 'list_start',
                'type' => 'boolean',
                'default' => false,
            ],
            'list_start_items' => [
                'id' => 'list_start_items',
                'type' => 'integer',
            ],
            'list_end' => [
                'id' => 'list_end',
                'type' => 'boolean',
                'default' => false,
            ],
            'list_end_items' => [
                'id' => 'list_end_items',
                'type' => 'integer',
            ],
            'custom_position' => [
                'id' => 'custom_position',
                'type' => 'boolean',
                'default' => false,
            ],
            'custom_position_items' => [
                'id' => 'custom_position_items',
                'type' => 'integer',
            ],
            'custom_position_insert_index' => [
                'id' => 'custom_position_insert_index',
                'type' => 'integer',
            ],
            'custom_position_per_page' => [
                'id' => 'custom_position_per_page',
                'type' => 'integer',
            ],
        ]
    ];

}
