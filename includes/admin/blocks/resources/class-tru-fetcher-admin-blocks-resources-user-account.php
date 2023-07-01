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
class Tru_Fetcher_Admin_Blocks_Resources_User_Account extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'user_account_block';
    public const BLOCK_NAME = 'tru-fetcher/user-account-block';
    public const BLOCK_TITLE = 'Tf User Account Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'component',
                'type' => 'string',
                'default' => 'dashboard',
            ],
            [
                'id' => 'tab_label',
                'type' => 'string',
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
        ]
    ];
}
