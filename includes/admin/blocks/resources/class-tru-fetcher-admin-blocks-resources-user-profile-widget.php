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
class Tru_Fetcher_Admin_Blocks_Resources_User_Profile_Widget extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'user_profile_widget_block';
    public const BLOCK_NAME = 'tru-fetcher/user-profile-widget-block';
    public const BLOCK_TITLE = 'Tf User Profile Widget Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
//        'ancestor' => [
//            Tru_Fetcher_Admin_Blocks_Resources_Sidebar_Widgets::BLOCK_NAME,
//            Tru_Fetcher_Admin_Blocks_Resources_Content_Widgets::BLOCK_NAME,
//        ],
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'heading',
                'type' => 'text',
            ],
            [
                'id' => 'view',
                'type' => 'text',
                'default' => 'display',
                'form_control' => 'select',
                'options' => [
                    ['label' => 'Display', 'value' => 'display'],
                    ['label' => 'Edit', 'value' => 'edit'],
                ],
            ],
        ]
    ];
}
