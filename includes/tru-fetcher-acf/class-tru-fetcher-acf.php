<?php
namespace TruFetcher\Includes\TruFetcherAcf;

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

    const ACF_FIELDS = [
        "api-data-keys",
        "api-category",
        "api-service",
        "api-provider"
    ];
    private string $jsonPathDir;
    public array $settings;

	public function __construct() {
	    $this->jsonPathDir = dirname( __FILE__ ) . "/acf-json";
        $this->settings = array(
            'version'	=> '1.0.0',
            'url'		=> plugin_dir_url( __FILE__ ),
            'path'		=> plugin_dir_path( __FILE__ )
        );
	}

	public function acf_init() {
        add_action('acf/include_field_types', [$this, 'includeField']); // v5
        add_filter('acf/settings/save_json', [$this, "saveAcfConfigJson"]);
        add_filter('acf/settings/load_json', [$this, "loadAcfConfigJson"]);
        add_filter('acf/format_value/name=form_item', [$this, 'my_acf_load_field'], 10, 3);
    }
    public function my_acf_load_field( $value, $post_id, $field  ) {
        if (!isset($value["form_control"])) {
            return $value;
        }
        switch ($value["form_control"]) {
            case "select_countries":
                $value["countries_list"] = Tru_Fetcher::getCountriesSelectArray();
                break;
        }
        return $value;
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


    public function includeField( $version = false ) {

        // support empty $version
        if( !$version ) $version = 5;

        // load textdomain
//        load_plugin_textdomain( 'TEXTDOMAIN', false, plugin_basename( dirname( __FILE__ ) ) . '/lang' );

        include_once('fields/class-tru-fetcher-acf-field-base.php');
        // include
        foreach (self::ACF_FIELDS as $fieldName) {
            if (isset($fieldName) && $fieldName !== "") {
                include_once(sprintf('fields/class-tru-fetcher-acf-field-%s-v%d.php', $fieldName, $version));
            }
        }
    }

}
