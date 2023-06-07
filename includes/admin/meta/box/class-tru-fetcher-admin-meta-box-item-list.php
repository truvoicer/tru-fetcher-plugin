<?php

namespace TruFetcher\Includes\Admin\Meta\Box;

use TruFetcher\Includes\Admin\PostTypes\Tru_Fetcher_Admin_Post_Types;
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
class Tru_Fetcher_Admin_Meta_Box_Item_List extends Tru_Fetcher_Base
{
    public const CONFIG = [
        'id' => 'item_list',
        'title' => 'Item List',
        'post_types' => [
            Tru_Fetcher_Admin_Post_Types::FETCHER_COMPARISON_LIST_PT,
        ],
        'fields' => [
            [
                'id' => 'item_list',
                'type' => 'array',
            ],
        ]
    ];
}
