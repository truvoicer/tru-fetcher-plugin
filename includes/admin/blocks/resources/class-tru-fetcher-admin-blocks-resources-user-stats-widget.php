<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Post_Types;
use TruFetcher\Includes\Admin\Resources\Tru_Fetcher_Admin_Resources_Taxonomies;
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
class Tru_Fetcher_Admin_Blocks_Resources_User_Stats_Widget extends Tru_Fetcher_Admin_Blocks_Resources_Base
{

    public array $config = [
        'id' => 'user-stats-widget-block',
        'name' => 'tru-fetcher/user-stats-widget-block',
        'title' => 'Tf User Stats Widget Block',
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'show_provider_stats',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'show_item_stats',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'show_saved_items_stats',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'provider_heading',
                'type' => 'text',
            ],
            [
                'id' => 'item_heading',
                'type' => 'text',
            ],
            [
                'id' => 'saved_items_heading',
                'type' => 'text',
            ],
        ]
    ];
}
