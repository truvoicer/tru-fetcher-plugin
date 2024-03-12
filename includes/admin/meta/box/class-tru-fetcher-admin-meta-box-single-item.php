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
    public const SERVICE_ID = 'service';
    public const DATA_KEYS_ID = 'data_keys';

    protected string $id = 'single_item';
    protected string $title = 'Single Item';

    protected array $config = [];

    public function __construct()
    {
        parent::__construct();
        $this->config =  [
            'id' => $this->id,
            'title' => $this->title,
            'post_types' => [
                ['name' => Tru_Fetcher_Post_Types_Trf_Single_Item::NAME, 'show' => true],
            ],
            'fields' => [
                [
                    'id' => self::SERVICE_ID,
                    'type' => 'integer',
                ],
                [
                    'id' => self::DATA_KEYS_ID,
                    'type' => 'array',
                ],
            ]
        ];
    }

    public function renderPost(\WP_Post $post) {
        $post = parent::renderPost($post);
        return $this->buildPostApiKeys($post);
    }
}
