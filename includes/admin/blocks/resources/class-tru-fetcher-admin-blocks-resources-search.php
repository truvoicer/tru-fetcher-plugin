<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Filter_List;

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
class Tru_Fetcher_Admin_Blocks_Resources_Search extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'search_block';
    public const BLOCK_NAME = 'tru-fetcher/search-block';
    public const BLOCK_TITLE = 'Tf Search Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Trf_Filter_List::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'search',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => Tru_Fetcher_Post_Types_Trf_Filter_List::ID_IDENTIFIER . '__search__categories',
                'type' => 'string',
                'default' => '',
                'form_control' => 'select',
            ],
            [
                'id' => 'search__categories_placeholder',
                'type' => 'string',
            ],
            [
                'id' => 'search__search_placeholder',
                'type' => 'string',
            ],
            [
                'id' => 'search__location_placeholder',
                'type' => 'string',
            ],
            [
                'id' => 'search__search_button_label',
                'type' => 'string',
            ],
            [
                'id' => 'search__featured_categories_label',
                'type' => 'string',
            ],
            [
                'id' => Tru_Fetcher_Post_Types_Trf_Filter_List::ID_IDENTIFIER . '__search__featured_categories',
                'type' => 'string',
                'default' => '',
                'form_control' => 'select',
            ],
            [
                'id' => 'listing_relation',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'listing_block_id',
                'type' => 'string',
            ],
        ]
    ];
}
