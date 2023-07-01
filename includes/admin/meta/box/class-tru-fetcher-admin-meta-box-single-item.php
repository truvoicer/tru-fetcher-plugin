<?php

namespace TruFetcher\Includes\Admin\Meta\Box;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Single_Item;
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
class Tru_Fetcher_Admin_Meta_Box_Single_Item extends Tru_Fetcher_Admin_Meta_Box_Base
{
    public const NAME = 'single_item';
    public const TITLE = 'Single Item';

    public const CONFIG = [
        'id' => self::NAME,
        'title' => self::TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Trf_Single_Item::NAME],
        ],
        'fields' => [
            [
                'id' => 'type',
                'type' => 'text',
            ],
            [
                'id' => 'service',
                'type' => 'integer',
            ],
            [
                'id' => 'data_keys',
                'type' => 'array',
            ],
            [
                'id' => 'item_image',
                'type' => 'text'
            ],
            [
                'id' => 'item_header',
                'type' => 'text'
            ],
            [
                'id' => 'item_text',
                'type' => 'text'
            ],
            [
                'id' => 'item_rating',
                'type' => 'number'
            ],
            [
                'id' => 'item_link_text',
                'type' => 'text'
            ],
            [
                'id' => 'item_link',
                'type' => 'text'
            ],
            [
                'id' => 'item_badge_text',
                'type' => 'text'
            ],
            [
                'id' => 'item_badge_link',
                'type' => 'text'
            ],
        ]
    ];
    protected array $config = self::CONFIG;

    public function buildMetaBoxFieldData(\WP_Post $post) {
        $buildFields = parent::buildMetaBoxFieldData($post);
        if (empty($buildFields['data_keys']) || !is_array($buildFields['data_keys'])) {
            return $buildFields;
        }
        $dataKeys = [];
        foreach ($buildFields['data_keys'] as $dataKey) {
            $dataKeys[$dataKey->data_item_key] = $dataKey->data_item_value;
        }
        $buildFields['data_keys'] = $dataKeys;
        return $buildFields;
    }
}
