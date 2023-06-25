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
class Tru_Fetcher_Admin_Blocks_Resources_Tabs extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'tabs_block';
    public const BLOCK_NAME = 'tru-fetcher/tabs-block';
    public const BLOCK_TITLE = 'Tf Tabs Block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'tabs_block_type',
                'type' => 'string',
                'default' => 'carousel',
            ],
            [
                'id' => 'tabs_orientation',
                'type' => 'string',
                'default' => 'vertical',
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'sub_heading',
                'type' => 'string',
            ],
            [
                'id' => 'cta',
                'type' => 'string',
            ],
            [
                'id' => 'tabs',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'request_options',
                'type' => 'object',
                'default' => null,
            ],
        ]
    ];
}
