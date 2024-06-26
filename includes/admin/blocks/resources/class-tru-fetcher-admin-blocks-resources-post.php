<?php

namespace TruFetcher\Includes\Admin\Blocks\Resources;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Page;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Category;

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
class Tru_Fetcher_Admin_Blocks_Resources_Post extends Tru_Fetcher_Admin_Blocks_Resources_Base
{
    public const BLOCK_ID = 'post_block';
    public const BLOCK_NAME = 'tru-fetcher/post-block';
    public const BLOCK_TITLE = 'Tf Post Block';

    public array $config = [
        'id' => self::BLOCK_ID,
        'name' => self::BLOCK_NAME,
        'title' => self::BLOCK_TITLE,
        'attributes' => [
            [
                'id' => 'heading',
                'type' => 'string',
            ],
            [
                'id' => 'params',
                'type' => 'array',
                'default' => [],
            ],
        ]
    ];

    public function __construct()
    {
        $this->config['attributes'] = array_merge($this->config['attributes'], Tru_Fetcher_Admin_Blocks_Resources_Base::getSidebarConfig());
    }
}
