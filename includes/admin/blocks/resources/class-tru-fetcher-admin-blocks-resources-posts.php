<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Category;

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
    public const BLOCK_TITLE = 'Tf Posts Block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [
            ['name' => Tru_Fetcher_Taxonomy_Category::NAME]
        ],
        'attributes' => [
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'load_more_type',
                'type' => 'string',
                'default' => 'pagination',
            ],
            [
                'id' => 'posts_per_page',
                'type' => 'integer',
            ],
            [
                'id' => 'show_sidebar',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'show_all_categories',
                'type' => 'boolean',
                'default' => true,
            ],
            [
                'id' => 'categories',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
