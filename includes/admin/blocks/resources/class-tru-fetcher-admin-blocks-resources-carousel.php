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
class Tru_Fetcher_Admin_Blocks_Resources_Carousel extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'carousel_block';
    public const BLOCK_NAME = 'tru-fetcher/carousel-block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => 'Tf Carousel Block',
        'ancestor' => [
            Tru_Fetcher_Admin_Blocks_Resources_Opt_In::BLOCK_NAME,
        ],
        'post_types' => [
            ['name' => Tru_Fetcher_Admin_Resources_Post_Types::FETCHER_ITEMS_LIST_PT]
        ],
        'taxonomies' => [],
        'attributes' => [
            'carousel_content' => [
                'id' => 'carousel_content',
                'type' => 'string',
                'default' => 'items',
            ],
            'item_list' => [
                'id' => 'item_list',
                'type' => 'integer',
            ],
            'carousel_heading' => [
                'id' => 'carousel_heading',
                'type' => 'string',
            ],
            'carousel_sub_heading' => [
                'id' => 'carousel_sub_heading',
                'type' => 'string',
            ],
            'request_name' => [
                'id' => 'request_name',
                'type' => 'string',
            ],
            'request_limit' => [
                'id' => 'request_limit',
                'type' => 'integer',
            ],
            'request_parameters' => [
                'id' => 'request_parameters',
                'type' => 'array',
                'default' => [],
            ],
            'carousel_settings' => [
                'id' => 'carousel_settings',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
