<?php
namespace TruFetcher\Includes\Admin;
use TrNewsApp\Includes\Admin\AdminMenu\Tr_News_App_Admin_Menu;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu;
use TruFetcher\Includes\Admin\Editor\Tru_Fetcher_Admin_Editor;
use TruFetcher\Includes\Admin\OldAdminMenu\Tru_Fetcher_Old_Admin_Menu;
use TruFetcher\Includes\Tru_Fetcher_Base;

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/admin
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Admin extends Tru_Fetcher_Base {


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct() {
	}


	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Tru_Fetcher_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Tru_Fetcher_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/tru-fetcher-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/tru-fetcher-admin.js', array( 'jquery' ), $this->version, false );

	}

    public function loadAdminMenu() {

        (new Tru_Fetcher_Admin_Menu())->init();
//        $truFetcherAdminMenu = new Tru_Fetcher_Old_Admin_Menu();
//        $truFetcherAdminMenu->admin_menu_init();
    }

    public function gb_gutenberg_admin_styles() {
        echo '
        <style>
            /* Main column width */
            .wp-block {
                max-width: 1080px;
            }
 
            /* Width of "wide" blocks */
            .wp-block[data-align="wide"] {
                max-width: 1080px;
            }
 
            /* Width of "full-wide" blocks */
            .wp-block[data-align="full"] {
                max-width: none;
            }	
        </style>
    ';
    }

    private function loadEditor() {
        (new Tru_Fetcher_Admin_Editor())->init();
    }
    public function init() {
        $this->loadAdminMenu();
        $this->loadEditor();
        add_action('admin_head', [$this, "gb_gutenberg_admin_styles"]);
    }
}
