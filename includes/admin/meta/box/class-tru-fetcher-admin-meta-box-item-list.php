<?php

namespace TruFetcher\Includes\Admin\Meta\Box;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Item_List;
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
class Tru_Fetcher_Admin_Meta_Box_Item_List extends Tru_Fetcher_Admin_Meta_Box_Base
{
    public const NAME = 'item_list';
    public const TITLE = 'Item List';

    public const CONFIG = [
        'id' => self::NAME,
        'title' => self::TITLE,
        'post_types' => [
            ['name' => Tru_Fetcher_Post_Types_Trf_Item_List::NAME],
        ],
        'fields' => [
            [
                'id' => 'item_list',
                'type' => 'array',
            ],
        ]
    ];
    protected array $config = self::CONFIG;
}
