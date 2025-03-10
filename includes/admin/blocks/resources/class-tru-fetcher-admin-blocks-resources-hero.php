<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Filter_List;

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
class Tru_Fetcher_Admin_Blocks_Resources_Hero extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'hero_block';
    public const BLOCK_NAME = 'tru-fetcher/hero-block';
    public const BLOCK_TITLE = 'Tf Hero Block';
    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Trf_Filter_List::NAME],
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'images',
                'type' => 'array',
            ],
            [
                'id' => 'hero_type',
                'type' => 'string',
                'default' => 'full_hero',
                'form_control' => 'select',
                'options' => [
                    ['value' => 'full_hero', 'label' => 'Full Hero'],
                    ['value' => 'breadcrumb_hero', 'label' => 'Breadcrumb Hero'],
                ],
            ],
            [
                'id' => 'hero_title',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'hero_text',
                'type' => 'string',
                'default' => '',
            ],
            [
                'id' => 'hero_extra_data',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];

    public function __construct()
    {
        $this->mergeConfigs([
            Tru_Fetcher_Admin_Blocks_Resources_Search::class,
        ]);
    }
}
