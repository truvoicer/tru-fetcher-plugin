<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_List;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Single_Item;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Trf_Listings_Category;

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
    public const BLOCK_TITLE = 'Tf Listings Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [
            [
                'name' => Tru_Fetcher_Post_Types_Trf_Item_List::NAME
            ],
            [
                'name' => Tru_Fetcher_Post_Types_Trf_Single_Item::NAME
            ],
        ],
        'taxonomies' => [
            ['name' => Tru_Fetcher_Taxonomy_Trf_Listings_Category::NAME],
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
                'id' => 'initial_search_term',
                'type' => 'string',
            ],
            [
                'id' => 'initial_load_search_params',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'initial_load_request_params',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'initial_load_request_name',
                'type' => 'string',
            ],
            [
                'id' => 'initial_load_request_limit',
                'type' => 'integer',
            ],
            [
                'id' => 'list_start',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'list_start_items',
                'type' => 'integer',
            ],
            [
                'id' => 'list_end',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'list_end_items',
                'type' => 'integer',
            ],
            [
                'id' => 'custom_position',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'custom_position_items',
                'type' => 'integer',
            ],
            [
                'id' => 'custom_position_insert_index',
                'type' => 'integer',
            ],
            [
                'id' => 'custom_position_per_page',
                'type' => 'integer',
            ],
        ]
    ];

}
