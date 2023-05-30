<?php

namespace TruFetcher\Includes\Admin\PostTypes;

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
class Tru_Fetcher_Admin_Post_Type_Loader {
    private string $postTypesDir = 'admin/includes/post-types/';

    private array $postTypesConfig = [];

    public function __construct() {
        $this->loadPostTypes();
    }

    public function loadPostTypes() {
        foreach ($this->postTypesConfig as $item) {
            $instance = new $item['className']();
            $instance->register();
        }
    }
}
