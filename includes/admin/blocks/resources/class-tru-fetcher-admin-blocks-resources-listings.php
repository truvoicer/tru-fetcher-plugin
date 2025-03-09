<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\Admin\Meta\Box\Tru_Fetcher_Admin_Meta_Box_Single_Item;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Keymaps;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_List;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Single_Item;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Category;
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
    private Tru_Fetcher_Api_Helpers_Keymaps $keymapHelpers;

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
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [
            ['name' => Tru_Fetcher_Taxonomy_Trf_Listings_Category::NAME],
            ['name' => Tru_Fetcher_Taxonomy_Category::NAME],
        ],
        'attributes' => [
            [
                'id' => 'listing_block_id',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'primary_listing',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'source',
                'type' => 'string',
                'default' => 'api',
            ],
            [
                'id' => 'api_fetch_type',
                'type' => 'string',
                'default' => 'database',
            ],
            [
                'id' => Tru_Fetcher_Taxonomy_Trf_Listings_Category::ID_IDENTIFIER,
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'api_listings_category',
                'type' => 'string',
            ],
            [
                'id' => 'api_listings_service',
                'type' => 'string',
            ],
            [
                'id' => 'thumbnail_type',
                'type' => 'string',
                'default' => 'disabled'
            ],
            [
                'id' => 'thumbnail_bg',
                'type' => 'string',
            ],
            [
                'id' => 'thumbnail_url',
                'type' => 'string',
            ],
            [
                'id' => 'thumbnail_key',
                'type' => 'string',
            ],
            [
                'id' => 'thumbnail_width',
                'type' => 'integer',
                'default' => 100
            ],
            [
                'id' => 'thumbnail_height',
                'type' => 'integer',
                'default' => 100
            ],
            [
                'id' => 'container_css',
                'type' => 'string',
                'default' => ''
            ],
            [
                'id' => 'sort_order',
                'type' => 'string',
            ],
            [
                'id' => 'sort_by',
                'type' => 'string',
            ],
            [
                'id' => 'date_key',
                'type' => 'string',
            ],
            [
                'id' => 'url_key',
                'type' => 'string',
                'default' => 'url',
            ],
            [
                'id' => 'title_key',
                'type' => 'string',
                'default' => 'title',
            ],
            [
                'id' => 'excerpt_key',
                'type' => 'string',
                'default' => 'excerpt',
            ],
            [
                'id' => 'description_key',
                'type' => 'string',
                'default' => 'description',
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
                'id' => 'display_as',
                'type' => 'string',
                'default' => 'list',
            ],
            [
                'id' => 'template',
                'type' => 'string',
                'default' => 'default',
            ],
            [
                'id' => 'grid_layout',
                'type' => 'string',
                'default' => 'list',
            ],
            [
                'id' => 'item_view_display',
                'type' => 'string',
                'default' => 'page',
            ],
            [
                'id' => 'link_type',
                'type' => 'string',
                'default' => 'direct',
            ],
            [
                'id' => 'load_more_type',
                'type' => 'string',
                'default' => 'infinite_scroll',
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
                'id' => Tru_Fetcher_Post_Types_Trf_Item_List::ID_IDENTIFIER,
                'type' => 'string',
                'default' => ''
            ],
            [
                'id' => 'wordpress_data_source',
                'type' => 'string',
            ],
            [
                'id' => 'posts_per_page',
                'type' => 'integer',
                'default' => 20,
            ],
            [
                'id' => 'show_all_categories',
                'type' => 'boolean',
                'default' => true,
            ],
            [
                'id' => Tru_Fetcher_Taxonomy_Category::ID_IDENTIFIER,
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'list_start',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' =>  Tru_Fetcher_Post_Types_Trf_Item_List::ID_IDENTIFIER . '__list_start_items',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'list_end',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => Tru_Fetcher_Post_Types_Trf_Item_List::ID_IDENTIFIER . '__list_end_items',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'custom_position',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => Tru_Fetcher_Post_Types_Trf_Item_List::ID_IDENTIFIER . '__custom_position_items',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'custom_position_insert_index',
                'type' => 'integer',
            ],
            [
                'id' => 'custom_position_per_page',
                'type' => 'integer',
            ],
            [
                'id' => 'show_filters_in_sidebar',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'show_sidebar_widgets_in_filters',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'show_filters',
                'type' => 'boolean',
                'default' => true,
            ],
            [
                'id' => 'filters_position',
                'type' => 'string',
                'default' => 'left',
            ],
            [
                'id' => 'filter_heading',
                'type' => 'string',
            ],

            [
                'id' => 'filters',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];

    public function __construct()
    {
        $this->keymapHelpers = new Tru_Fetcher_Api_Helpers_Keymaps();
        $this->config['attributes'] = array_merge($this->config['attributes'], parent::getSidebarConfig());
    }

    public function buildBlockAttributes(array $attributes, ?bool $includeDefaults = true, ?string $content = null, $block = null) {
        $attributes = parent::buildBlockAttributes($attributes);
        $attributes['keymap'] = [];

        if (!empty($attributes['api_listings_service'])) {
            $findKeymap = $this->keymapHelpers->getKeymap((int)$attributes['api_listings_service']);
            $attributes['keymap'] = $this->keymapHelpers->flattenKeymap($findKeymap);
        }

        if (empty($attributes['filters'])) {
            return $attributes;
        }
        if (!is_array($attributes['filters'])) {
            return $attributes;
        }
        $attributes['filters'] = array_map(function($filter) {
            return parent::buildBlockAttributes($filter, false);
        }, $attributes['filters']);
//        var_dump($attributes['filters']); die;
        return $attributes;
    }

}
