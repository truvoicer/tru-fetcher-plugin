<?php

namespace TruFetcher\Includes\Admin\Meta\Box;

use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types;
use TruFetcher\Includes\PostTypes\Tru_Fetcher_Post_Types_Trf_Filter_List;
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
class Tru_Fetcher_Admin_Meta_Box_Filter_Lists extends Tru_Fetcher_Admin_Meta_Box_Base
{

    protected string $id = 'filter_lists';
    protected string $title = 'Filter Lists';

    protected array $config = [];

    public function __construct()
    {
        parent::__construct();
        $this->config = [
            'id' => $this->id,
            'title' => $this->title,
            'post_types' => [
                ['name' => Tru_Fetcher_Post_Types_Trf_Filter_List::NAME, 'show' => true],
            ],
            'fields' => [
                [
                    'id' => 'list_items',
                    'type' => 'array',
                ],
            ]
        ];
    }
}
