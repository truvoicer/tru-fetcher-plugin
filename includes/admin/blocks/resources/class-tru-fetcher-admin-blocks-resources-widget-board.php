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

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => 'Tf Widget Board Block',
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            'show_sidebar' => [
                'id' => 'show_sidebar',
                'type' => 'boolean',
                'default' => false,
            ],
            'heading' => [
                'id' => 'heading',
                'type' => 'string',
            ],
            'content_widgets' => [
                'id' => 'content_widgets',
                'type' => 'array',
                'default' => [],
            ],
            'sidebar_widgets' => [
                'id' => 'sidebar_widgets',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];
}
