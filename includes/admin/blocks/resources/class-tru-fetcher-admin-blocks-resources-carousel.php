<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_List;

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
    public const BLOCK_TITLE = 'Tf Carousel Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'ancestor' => [
            Tru_Fetcher_Admin_Blocks_Resources_Opt_In::BLOCK_NAME,
        ],
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Trf_Item_List::NAME],
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'carousel_content',
                'type' => 'string',
                'default' => 'items',
            ],
            [
                'id' => Tru_Fetcher_Post_Types_Trf_Item_List::ID_IDENTIFIER,
                'type' => 'integer',
            ],
            [
                'id' => 'carousel_heading',
                'type' => 'string',
            ],
            [
                'id' => 'carousel_sub_heading',
                'type' => 'string',
            ],
            [
                'id' => 'request_name',
                'type' => 'string',
            ],
            [
                'id' => 'request_limit',
                'type' => 'integer',
            ],
            [
                'id' => 'request_parameters',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'carousel_settings',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
