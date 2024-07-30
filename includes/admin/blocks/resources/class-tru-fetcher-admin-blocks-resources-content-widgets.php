<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;

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
class Tru_Fetcher_Admin_Blocks_Resources_Content_Widgets extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'content_widgets_block';
    public const BLOCK_NAME = 'tru-fetcher/content-widgets-block';
    public const BLOCK_TITLE = 'Tf Content Widgets Block';

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
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'container' => 'widgets'
    ];
}
