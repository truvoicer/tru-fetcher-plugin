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
class Tru_Fetcher_Admin_Blocks_Resources_User_Account extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'user_account_block';
    public const BLOCK_NAME = 'tru-fetcher/user-account-block';
    public const BLOCK_TITLE = 'Tf User Account Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'component',
                'type' => 'string',
                'default' => 'dashboard',
                'form_control' => 'select',
                'options' => [
                    ['label' => 'Dashboard', 'value' => 'dashboard'],
                    ['label' => 'User Profile', 'value' => 'user_profile'],
                    ['label' => 'Account Details', 'value' => 'account_details'],
                    ['label' => 'Saved Items', 'value' => 'saved_items'],
                    ['label' => 'Messages', 'value' => 'messages'],
                ],
            ],
            [
                'id' => 'tab_label',
                'type' => 'string',
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'tabs_orientation',
                'type' => 'string',
                'default' => 'vertical',
                'form_control' => 'select',
                'options' => [
                    ['label' => 'Vertical', 'value' => 'vertical'],
                    ['label' => 'Horizontal', 'value' => 'horizontal'],
                ],
            ],
        ]
    ];
}
