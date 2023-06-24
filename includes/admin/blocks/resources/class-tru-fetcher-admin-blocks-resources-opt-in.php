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
    public const BLOCK_ID = 'opt_in_block';
    public const BLOCK_NAME = 'tru-fetcher/opt-in-block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => 'Tf Opt In Block',
        'children' => [
            Tru_Fetcher_Admin_Blocks_Resources_Form::BLOCK_NAME,
            Tru_Fetcher_Admin_Blocks_Resources_Carousel::BLOCK_NAME,
        ],
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            'optin_type' => [
                'id' => 'optin_type',
                'type' => 'string',
                'default' => 'form',
            ],
            'show_carousel' => [
                'id' => 'show_carousel',
                'type' => 'boolean',
                'default' => false,
            ],
            'heading' => [
                'id' => 'heading',
                'type' => 'string',
            ],
            'text' => [
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
