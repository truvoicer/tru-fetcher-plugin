<?php

namespace TruFetcher\Includes\Sidebars\widgets;

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
class Tru_Fetcher_Sidebar_Widgets_Listings extends Tru_Fetcher_Sidebar_Widgets_Base
{

    protected string $id = 'listings';
    protected string $title = 'Listings';

    protected array $config = [];

    public function __construct()
    {
        parent::__construct();
        $this->config = [
            'id' => $this->id,
            'title' => $this->title,
            'post_types' => [],
            'fields' => [
                [
                    'id' => 'list_items',
                    'type' => 'array',
                ],
            ]
        ];
    }
}
