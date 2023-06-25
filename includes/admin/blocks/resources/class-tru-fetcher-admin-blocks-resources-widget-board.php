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
class Tru_Fetcher_Admin_Blocks_Resources_Widget_Board extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'widget_board_block';
    public const BLOCK_NAME = 'tru-fetcher/widget-board-block';
    public const BLOCK_TITLE = 'Tf Widget Board Block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
//        'children' => [
//            Tru_Fetcher_Admin_Blocks_Resources_Sidebar_Widgets::class,
//            Tru_Fetcher_Admin_Blocks_Resources_Content_Widgets::class,
//        ],
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'show_sidebar',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'content_widgets',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'sidebar_widgets',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
