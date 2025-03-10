<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;

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
    public const BLOCK_TITLE = 'Tf Opt In Block';
    public const BLOCK_NAME = 'tru-fetcher/opt-in-block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
//        'children' => [
//            Tru_Fetcher_Admin_Blocks_Resources_Form::class,
//            Tru_Fetcher_Admin_Blocks_Resources_Carousel::class,
//        ],
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'optin_type',
                'type' => 'string',
                'default' => 'form',
                'form_control' => 'select',
                'options' => [
                    [
                        'label' => 'Form',
                        'value' => 'form',
                    ],
                ],
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
    public function buildBlockAttributes(array $attributes, ?bool $includeDefaults = true, ?string $content = null, $block = null) {
        $attributes = parent::buildBlockAttributes($attributes);

        $blocks = [
            Tru_Fetcher_Admin_Blocks_Resources_Carousel::class,
            Tru_Fetcher_Admin_Blocks_Resources_Form::class,
        ];
        foreach ($blocks as $block) {
            if (isset($attributes[$block::BLOCK_ID]) && is_array($attributes[$block::BLOCK_ID])) {
                $blockInstance = new $block();
                $build = $blockInstance->buildBlockAttributes($attributes[$block::BLOCK_ID]);
                $attributes[$block::BLOCK_ID] = $build;
            }
        }
        return $attributes;
    }
}
