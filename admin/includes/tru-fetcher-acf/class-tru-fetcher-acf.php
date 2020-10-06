<?php

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
class Tru_Fetcher_Acf {
    private string $jsonPathDir;

	public function __construct() {
	    $this->jsonPathDir = dirname( __FILE__ ) . "/acf-json";
	}

	public function acf_init() {
        add_filter('acf/settings/save_json', [$this, "saveAcfConfigJson"]);
        add_filter('acf/settings/load_json', [$this, "loadAcfConfigJson"]);
    }

    public function saveAcfConfigJson($path) {
        // update path
        $path = $this->jsonPathDir;

        // return
        return $path;
    }

    public function loadAcfConfigJson($paths) {
        // remove original path (optional)
        unset($paths[0]);

        // append path
        $paths[] = $this->jsonPathDir;

        // return
        return $paths;
    }

}
