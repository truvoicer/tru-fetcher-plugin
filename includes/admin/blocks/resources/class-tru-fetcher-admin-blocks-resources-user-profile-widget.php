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
class Tru_Fetcher_Admin_Blocks_Resources_User_Profile_Widget extends Tru_Fetcher_Admin_Blocks_Resources_Base
{

    public array $config = [
        'id' => 'user-profile-widget-block',
        'name' => 'tru-fetcher/user-profile-widget-block',
        'title' => 'Tf User Profile Widget Block',
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'heading',
                'type' => 'text',
            ],
        ]
    ];
}
