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
class Tru_Fetcher_Admin_Blocks_Resources_Posts extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'posts_block';
    public const BLOCK_NAME = 'tru-fetcher/posts-block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => 'Tf Posts Block',
        'post_types' => [],
        'taxonomies' => [
            ['name' => Tru_Fetcher_Admin_Resources_Taxonomies::CATEGORY_TAXONOMY]
        ],
        'attributes' => [
            'heading' => [
                'id' => 'heading',
                'type' => 'string',
            ],
            'load_more_type' => [
                'id' => 'load_more_type',
                'type' => 'string',
                'default' => 'pagination',
            ],
            'posts_per_page' => [
                'id' => 'posts_per_page',
                'type' => 'integer',
            ],
            'show_sidebar' => [
                'id' => 'show_sidebar',
                'type' => 'boolean',
                'default' => false,
            ],
            'show_all_categories' => [
                'id' => 'show_all_categories',
                'type' => 'boolean',
                'default' => true,
            ],
            'categories' => [
                'id' => 'categories',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
