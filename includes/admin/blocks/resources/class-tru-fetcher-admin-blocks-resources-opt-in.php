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
class Tru_Fetcher_Admin_Blocks_Resources_Opt_In extends Tru_Fetcher_Admin_Blocks_Resources_Base
{

    public array $config = [
        'id' => 'opt-in-block',
        'name' => 'tru-fetcher/opt-in-block',
        'title' => 'Tf Opt In Block',
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'optin_type',
                'type' => 'string',
                'default' => 'form',
            ],
            [
                'id' => 'show_carousel',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'text',
                'type' => 'string',
            ],
        ]
    ];

    public function __construct()
    {
        $this->mergeConfigs([
            Tru_Fetcher_Admin_Blocks_Resources_Form::class,
            Tru_Fetcher_Admin_Blocks_Resources_Carousel::class,
        ]);
    }
}
