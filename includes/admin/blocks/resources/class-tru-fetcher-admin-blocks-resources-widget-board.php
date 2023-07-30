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
class Tru_Fetcher_Admin_Blocks_Resources_Widget_Board extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'widget_board_block';
    public const BLOCK_NAME = 'tru-fetcher/widget-board-block';
    public const BLOCK_TITLE = 'Tf Widget Board Block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
//        'children' => [
//            Tru_Fetcher_Admin_Blocks_Resources_Sidebar_Widgets::class,
//            Tru_Fetcher_Admin_Blocks_Resources_Content_Widgets::class,
//        ],
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'show_sidebar',
                'type' => 'boolean',
                'default' => false,
            ],
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'content_widgets',
                'type' => 'array',
                'default' => [],
            ],
            [
                'id' => 'sidebar_widgets',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];

    public function buildBlockAttributes(array $attributes)
    {
        $buildAttributes = parent::buildBlockAttributes($attributes);
        $buildAttributes = $this->buildWidgetAttributes('content_widgets', $buildAttributes);
        $buildAttributes = $this->buildWidgetAttributes('sidebar_widgets', $buildAttributes);
        return $buildAttributes;
    }

    private function buildWidgetAttributes(string $attributeId, array $attributes) {
        if (empty($attributeId) || empty($attributes[$attributeId])) {
            return $attributes;
        }
        $blocks = [
            Tru_Fetcher_Admin_Blocks_Resources_Tabs::class,
        ];
        foreach ($attributes[$attributeId] as $key => $tab) {
            foreach ($blocks as $block) {
                if (isset($tab['block_id']) && $tab['block_id'] === $block::BLOCK_ID) {
                    $blockInstance = new $block();
                    $build = $blockInstance->buildBlockAttributes($tab);
                    $attributes[$attributeId][$key] = array_merge($attributes[$attributeId][$key], $build);
                }
            }
        }
        return $attributes;
    }
}
