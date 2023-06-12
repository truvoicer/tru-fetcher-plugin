<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

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
class Tru_Fetcher_Admin_Blocks_Resources_Listings
{
    public const CONFIG = [
        'id' => 'listings',
        'name' => 'tru-fetcher/listings-block',
        'title' => 'Tf Listings Block',
        'attributes' => [
            [
                'id' => 'source',
                'type' => 'string',
            ],
            [
                'id' => 'type',
                'type' => 'string',
            ],
            [
                'id' => 'category',
                'type' => 'string',
            ],
            [
                'id' => 'select_providers',
                'type' => 'boolean',
            ],
            [
                'id' => 'providers_list',
                'type' => 'array',
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'item_view_display',
                'type' => 'string',
            ],
            [
                'id' => 'load_more_type',
                'type' => 'string',
            ],
            [
                'id' => 'show_filters_toggle',
                'type' => 'boolean',
            ],
            [
                'id' => 'search_limit',
                'type' => 'integer',
            ],
            [
                'id' => 'initial_load',
                'type' => 'string',
            ],
        ]
    ];
}
