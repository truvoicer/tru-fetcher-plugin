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
    public const BLOCK_ID = 'hero_block';
    public const BLOCK_NAME = 'tru-fetcher/hero-block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => 'Tf Hero Block',
        'post_types' => [
            ['name' => Tru_Fetcher_Admin_Resources_Post_Types::FETCHER_FILTER_LISTS_PT]
        ],
        'taxonomies' => [],
        'attributes' => [
            'hero_background_image' => [
                'id' => 'hero_background_image',
                'type' => 'string',
            ],
            'hero_background_image_2' => [
                'id' => 'hero_background_image_2',
                'type' => 'string',
                'default' => 'search',
            ],
            'hero_background_image_3' => [
                'id' => 'hero_background_image_3',
                'type' => 'string',
                'default' => '',
            ],
            'hero_type' => [
                'id' => 'hero_type',
                'type' => 'string',
                'default' => '',
            ],
            'hero_title' => [
                'id' => 'hero_title',
                'type' => 'string',
                'default' => '',
            ],
            'hero_text' => [
                'id' => 'hero_text',
                'type' => 'string',
                'default' => '',
            ],
            'hero_search' => [
                'id' => 'hero_search',
                'type' => 'boolean',
                'default' => false,
            ],
            'hero_search__categories' => [
                'id' => 'hero_search__categories',
                'type' => 'integer',
            ],
            'hero_search__categories_placeholder' => [
                'id' => 'hero_search__categories_placeholder',
                'type' => 'string',
            ],
            'hero_search__search_placeholder' => [
                'id' => 'hero_search__search_placeholder',
                'type' => 'string',
            ],
            'hero_search__location_placeholder' => [
                'id' => 'hero_search__location_placeholder',
                'type' => 'string',
            ],
            'hero_search__search_button_label' => [
                'id' => 'hero_search__search_button_label',
                'type' => 'string',
            ],
            'hero_search__featured_categories_label' => [
                'id' => 'hero_search__featured_categories_label',
                'type' => 'string',
            ],
            'hero_search__featured_categories' => [
                'id' => 'hero_search__featured_categories',
                'type' => 'integer',
            ],
            'hero_extra_data' => [
                'id' => 'hero_extra_data',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];

}
