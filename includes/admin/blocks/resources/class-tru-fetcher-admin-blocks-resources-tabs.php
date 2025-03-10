<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Tab_Presets;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Form_Presets;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Tab_Presets;
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
class Tru_Fetcher_Admin_Blocks_Resources_Tabs extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    private Tru_Fetcher_DB_Repository_Tab_Presets $tabPresetsRepository;

    public const BLOCK_ID = 'tabs_block';
    public const BLOCK_NAME = 'tru-fetcher/tabs-block';
    public const BLOCK_TITLE = 'Tf Tabs Block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Page::NAME],
        ],
        'taxonomies' => [],
        'attributes' => [
            [
                'id' => 'presets',
                'type' => 'string',
                'default' => 'custom',
                'form_control' => 'select',
                'options' => [
                    [
                        'label' => 'Custom',
                        'value' => 'custom',
                    ],
                    //...
                ]
            ],
            [
                'id' => 'tabs_block_type',
                'type' => 'string',
                'default' => 'carousel',
                'form_control' => 'select',
                'options' => [
                    [
                        'label' => 'Carousel',
                        'value' => 'carousel',
                    ],
                    [
                        'label' => 'Video',
                        'value' => 'video',
                    ],
                    [
                        'label' => 'Custom',
                        'value' => 'custom',
                    ],
                ]
            ],
            [
                'id' => 'tabs_orientation',
                'type' => 'string',
                'default' => 'vertical',
                'form_control' => 'select',
                'options' => [
                    [
                        'label' => 'Vertical',
                        'value' => 'vertical',
                    ],
                    [
                        'label' => 'Horizontal',
                        'value' => 'horizontal',
                    ],
                ]
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

    public function __construct()
    {
        $this->tabPresetsRepository = new Tru_Fetcher_DB_Repository_Tab_Presets();
    }

    public function buildBlockAttributes(array $attributes, ?bool $includeDefaults = true, ?string $content = null, $block = null)
    {
        $buildAttributes = parent::buildBlockAttributes($attributes);

        if (!empty($buildAttributes['presets']) && $buildAttributes['presets'] !== 'custom') {
            $preset = (new Tru_Fetcher_Api_Helpers_Tab_Presets())
                ->getTabPresetsRepository()
                ->findById((int)$buildAttributes['presets']);
            if (!empty($preset['config_data'])) {
                $buildAttributes = [
                    ...$buildAttributes,
                    ...$this->tabPresetsRepository->buildTabPresetConfigData($preset['config_data'])
                ];
            }
            return $buildAttributes;
        }
        $blocks = [
            Tru_Fetcher_Admin_Blocks_Resources_Carousel::class,
            Tru_Fetcher_Admin_Blocks_Resources_Form::class,
        ];
        foreach ($buildAttributes['tabs'] as $key => $tab) {
            foreach ($blocks as $block) {
                if (isset($tab[$block::BLOCK_ID]) && is_array($tab[$block::BLOCK_ID])) {
                    $blockInstance = new $block();
                    $build = $blockInstance->buildBlockAttributes($tab[$block::BLOCK_ID]);
                    $buildAttributes['tabs'][$key][$block::BLOCK_ID] = $build;
                }
            }
        }

        return $buildAttributes;
    }

}
