<?php
namespace TruFetcher\Includes;

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

use DirectoryIterator;
use TruFetcher\Includes\Admin\Meta\Tru_Fetcher_Admin_Meta;
use TruFetcher\Includes\Admin\Tru_Fetcher_Admin;
use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth_Jwt;
use TruFetcher\Includes\Api\Tru_Fetcher_Api;
use TruFetcher\Includes\Blocks\Tru_Fetcher_Blocks;
use TruFetcher\Includes\Taxonomy\Tru_Fetcher_Taxonomy;
use TruFetcher\Includes\User\Tru_Fetcher_User;

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher extends Tru_Fetcher_Base {

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {
        parent::__construct();
		$this->set_locale();
		$this->loadAdmin();
		$this->loadApi();
		$this->define_post_types();
		$this->define_blocks();
		$this->define_nav_menus();
		$this->define_sidebars();
		$this->define_widgets();

	}


	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Tru_Fetcher_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Tru_Fetcher_i18n();
		add_action( 'plugins_loaded', [$plugin_i18n, 'load_plugin_textdomain'] );

	}

    private function loadAdmin() {
        $admin = new Tru_Fetcher_Admin();
        $admin->init();
    }

	public static function getFrontendUrl() {
//		$options = \get_fields_clone( "option" );
		$frontendUrl = get_option( 'siteurl' );
		if ( isset( $options["frontend_url"] ) ) {
			$frontendUrl = $options["frontend_url"];
		}
		return $frontendUrl;
	}

	public static function getCountriesListArray() {
        return include plugin_dir_path( dirname( __FILE__ ) ) . 'config/country-list.php';
    }

	public static function getCountriesSelectArray() {
	    $selectList = [];
	    foreach (self::getCountriesListArray() as $code => $country) {
	        array_push($selectList, [
	           "value" =>  $code,
                "label" => $country
            ]);
        }
	    return $selectList;
	}

    public static function isNotEmpty(string $string = null) {
        if (isset( $string ) && $string !== "") {
            return true;
        }
        return false;
    }

	private function loadApi() {
		$truFetcherEndpoints = new Tru_Fetcher_Api();
		$truFetcherEndpoints->init();
	}

    private function loadEmail() {
//        $truFetcherEmail = new Tru_Fetcher_Email();
//	    $truFetcherEmail->init();
    }

	private function define_post_types() {
        $this->directoryIncludes( 'includes/post-types', 'register-post-type.php' );
	}

	private function define_blocks() {
	    $blocksManager = new Tru_Fetcher_Blocks();
	    $blocksManager->blocks_init();
	}

	private function define_nav_menus() {
		$this->directoryIncludes( 'includes/menus/register', 'register-menu.php' );
	}

	private function define_widgets() {
		$this->directoryIncludes( 'includes/widgets', 'register-widget.php' );
	}

	private function define_sidebars() {
		$this->directoryIncludes( 'includes/sidebars/register', 'register-sidebar.php' );
	}

    public static function directoryIncludes($pathName, $fileName)
    {
        $dir = new DirectoryIterator(TRU_FETCHER_PLUGIN_DIR . $pathName);
        foreach ($dir as $fileinfo) {
            if ($fileinfo->isDot()) {
                continue;
            }
            $fileDir = $fileinfo->getRealPath() . '/' . $fileName;
            if (file_exists($fileDir)) {
                require_once($fileDir);
            }
        }
    }

    public function activate($plugin, $network_wide) {
        $this->healthCheck->setIsNetworkWide($network_wide);
        $run = $this->healthCheck->runHealthCheck();

        if ($run) {
            return true;
        }
        $install = $this->healthCheck->initialInstall();
        if (!$install) {
            error_log(json_encode([TRU_FETCHER_PLUGIN_NAME => 'Health check install failed']));
            return false;
        }
        return true;
    }

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {

	}
}
