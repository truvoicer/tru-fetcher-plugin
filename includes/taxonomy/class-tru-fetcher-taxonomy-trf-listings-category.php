<?php

namespace TruFetcher\Includes\Taxonomy;

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
class Tru_Fetcher_Taxonomy_Trf_Listings_Category extends Tru_Fetcher_Taxonomy_Base
{
    public const NAME = 'trf_listings_category';
    public const ID_IDENTIFIER = 'listings_category_id';
    protected string $name = self::NAME;
    protected string $idIdentifier = self::ID_IDENTIFIER;

    public function init()
    {
        $this->registerTaxonomy(
            'Listings Categories',
            'Listings Category',
            ['item_view_templates', 'pages', 'posts'],
        );
    }

}
