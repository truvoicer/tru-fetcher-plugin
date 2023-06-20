<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

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
class Tru_Fetcher_Admin_Blocks_Resources_Hero extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public array $config = [
        'id' => 'hero-block',
        'name' => 'tru-fetcher/hero-block',
        'title' => 'Tf Hero Block',
        'post_types' => [
            ['name' => Tru_Fetcher_Admin_Resources_Post_Types::FETCHER_FILTER_LISTS_PT]
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
                'default' => '',
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
                'id' => 'hero_search__categories',
                'type' => 'integer',
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
                'id' => 'hero_search__featured_categories',
                'type' => 'integer',
            ],
            [
                'id' => 'hero_extra_data',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];

}
