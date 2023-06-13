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
        'id' => 'listings-block',
        'name' => 'tru-fetcher/listings-block',
        'title' => 'Tf Listings Block',
        'attributes' => [
            [
                'id' => 'source',
                'type' => 'string',
                'default' => 'api',
            ],
            [
                'id' => 'api_type',
                'type' => 'string',
                'default' => 'search',
            ],
            [
                'id' => 'category',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'select_providers',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'providers_list',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'heading',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'item_view_display',
                'type' => 'string',
                'default' => 'page',
            ],
            [
                'id' => 'load_more_type',
                'type' => 'string',
                'default' => 'infinite_scroll',
            ],
            [
                'id' => 'show_filters_toggle',
                'type' => 'boolean',
                'default' => true,
            ],
            [
                'id' => 'search_limit',
                'type' => 'integer',
                'default' => 20,
            ],
            [
                'id' => 'initial_load',
                'type' => 'string',
                'default' => 'search',
            ],
        ]
    ];

    public function renderBlock( $block_attributes, $content ) {
        var_dump($block_attributes);
        return '<div class="tru-fetcher-listings-block">sfdsfdsfdsfd</div>';
    }
}
