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
class Tru_Fetcher_Admin_Meta_Box_Filter_Lists extends Tru_Fetcher_Base
{
    public const CONFIG = [
        'id' => 'filter_lists',
        'title' => 'Filter Lists',
        'post_types' => [
            ['name' => Tru_Fetcher_Admin_Resources_Post_Types::FETCHER_FILTER_LISTS_PT],
        ],
        'fields' => [
            [
                'id' => 'list_items',
                'type' => 'array',
            ],
        ]
    ];
}
