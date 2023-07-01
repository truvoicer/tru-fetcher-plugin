<?php

namespace TruFetcher\Includes\PostTypes;

use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy_Trf_Listings_Category;

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
class Tru_Fetcher_Post_Types_Trf_Single_Item extends Tru_Fetcher_Post_Types_Base
{
    public const NAME = 'trf_single_item';
    protected string $name = self::NAME;

    public function init()
    {
        $this->registerPostType(
            'Single Item',
            'Single Item',
            [
                'supports' => array('title'),
                'taxonomies' => [
                    Tru_Fetcher_Taxonomy_Trf_Listings_Category::NAME
                ],
                'menu_position' => 5,
                'capability_type' => 'page',
            ]
        );
    }

}
