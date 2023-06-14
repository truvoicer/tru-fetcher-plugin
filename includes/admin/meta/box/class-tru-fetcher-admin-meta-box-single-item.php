<?php

namespace TruFetcher\Includes\Admin\Meta\Box;

use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Post_Types;
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
class Tru_Fetcher_Admin_Meta_Box_Single_Item extends Tru_Fetcher_Base
{
    public const CONFIG = [
        'id' => 'single_item',
        'title' => 'Single Item',
        'post_types' => [
            ['name' => Tru_Fetcher_Admin_Resources_Post_Types::FETCHER_SINGLE_ITEM_PT],
        ],
        'fields' => [
            [
                'id' => 'type',
                'type' => 'text',
            ],
            [
                'id' => 'data_keys',
                'type' => 'array',
            ],
            [
                'id' => 'item_image',
                'type' => 'text'
            ],
            [
                'id' => 'item_header',
                'type' => 'text'
            ],
            [
                'id' => 'item_text',
                'type' => 'text'
            ],
            [
                'id' => 'item_rating',
                'type' => 'number'
            ],
            [
                'id' => 'item_link_text',
                'type' => 'text'
            ],
            [
                'id' => 'item_link',
                'type' => 'text'
            ],
            [
                'id' => 'item_badge_text',
                'type' => 'text'
            ],
            [
                'id' => 'item_badge_link',
                'type' => 'text'
            ],
        ]
    ];
}
