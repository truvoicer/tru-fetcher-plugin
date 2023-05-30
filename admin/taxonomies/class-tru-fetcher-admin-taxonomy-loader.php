<?php

namespace TruFetcher\Includes\Admin\Taxonomies;

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
class Tru_Fetcher_Admin_Taxonomy_Loader {
    private string $taxonomiesDir = 'admin/includes/taxonomies/';

    private array $taxonomyConfig = [
      [
          'className' => Tru_Fetcher_Categories_Taxonomy::class,
          'file' => 'class-tr-news-app-categories-taxonomy'
      ]
    ];

    public function __construct() {
        $this->loadTaxonomies();
    }

    public function loadTaxonomies() {
        foreach ($this->taxonomyConfig as $item) {
            $instance = new $item['className']();
            $instance->register();
        }
    }
}
