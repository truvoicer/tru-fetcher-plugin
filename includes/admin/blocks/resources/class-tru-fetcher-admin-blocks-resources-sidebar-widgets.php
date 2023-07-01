<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

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
class Tru_Fetcher_Admin_Blocks_Resources_Sidebar_Widgets extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'sidebar_widgets_block';
    public const BLOCK_NAME = 'tru-fetcher/sidebar-widgets-block';
    public const BLOCK_TITLE = 'Tf Sidebar Widgets Block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'ancestor' => [
            Tru_Fetcher_Admin_Blocks_Resources_Widget_Board::BLOCK_NAME,
        ],
        'children' => [
            Tru_Fetcher_Admin_Blocks_Resources_Form_Progress_Widget::class,
            Tru_Fetcher_Admin_Blocks_Resources_User_Profile_Widget::class,
            Tru_Fetcher_Admin_Blocks_Resources_User_Social_Widget::class,
            Tru_Fetcher_Admin_Blocks_Resources_User_Stats_Widget::class,
        ],
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'widgets',
                'type' => 'array',
                'default' => [],
            ]
        ]
    ];
}
