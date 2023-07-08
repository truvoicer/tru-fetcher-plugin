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
class Tru_Fetcher_Admin_Blocks_Resources_Hero extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'hero_block';
    public const BLOCK_NAME = 'tru-fetcher/hero-block';
    public const BLOCK_TITLE = 'Tf Hero Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Trf_Filter_List::NAME],
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'hero_background_image',
                'type' => 'string',
            ],
            [
                'id' => 'hero_background_image_2',
                'type' => 'string',
                'default' => 'search',
            ],
            [
                'id' => 'hero_background_image_3',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'hero_type',
                'type' => 'string',
                'default' => 'full_hero',
            ],
            [
                'id' => 'hero_title',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'hero_text',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'hero_search',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => Tru_Fetcher_Post_Types_Trf_Filter_List::ID_IDENTIFIER . '__hero_search__categories',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'hero_search__categories_placeholder',
                'type' => 'string',
            ],
            [
                'id' => 'hero_search__search_placeholder',
                'type' => 'string',
            ],
            [
                'id' => 'hero_search__location_placeholder',
                'type' => 'string',
            ],
            [
                'id' => 'hero_search__search_button_label',
                'type' => 'string',
            ],
            [
                'id' => 'hero_search__featured_categories_label',
                'type' => 'string',
            ],
            [
                'id' => Tru_Fetcher_Post_Types_Trf_Filter_List::ID_IDENTIFIER . '__hero_search__featured_categories',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'hero_extra_data',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];

}
