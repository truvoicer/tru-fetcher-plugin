<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://truvoicer.co.uk
 * @since             1.0.0
 * @package           Tru_Fetcher
 *
 * @wordpress-plugin
 * Plugin Name:       Tru Fetcher Base Plugin
 * Plugin URI:        https://truvoicer.co.uk
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Michael
 * Author URI:        https://truvoicer.co.uk
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tru-fetcher
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
use TruFetcher\Includes\Tru_Fetcher;
use TruFetcher\Includes\Tru_Fetcher_Activator;
use TruFetcher\Includes\Tru_Fetcher_Deactivator;

if ( ! defined( 'WPINC' ) ) {
	die;
}
require_once __DIR__ . '/vendor/autoload.php';
/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
const TRU_FETCHER_PLUGIN_NAME = 'tru_fetcher';
const TRU_FETCHER_PLUGIN_NAME_ACRONYM = 'trf';
const TRU_FETCHER_ERROR_PREFIX = 'tru_fetcher_error';
define( 'TRU_FETCHER_VERSION', '1.0.0' );
define( 'TRU_FETCHER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'TRU_FETCHER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'TRU_FETCHER_PLUGIN_ADMIN_URL', TRU_FETCHER_PLUGIN_URL . 'admin' );
define( 'TRU_FETCHER_PLUGIN_ADMIN_DIR', plugin_dir_path( __FILE__ ) . 'includes/admin' );
define( 'TRU_FETCHER_PLUGIN_ADMIN_RES_DIR', TRU_FETCHER_PLUGIN_ADMIN_DIR . '/resources' );


function get_fields_clone()
{
    return false;
}
//if (!function_exists('get_fields')) {
//    function get_fields()
//    {
//        return false;
//    }
//}
function class_loader_init() {
    require_once plugin_dir_path( __FILE__ ) . 'includes/class-tru-fetcher-auto-loader.php';
    spl_autoload_register(
        [new Tru_Fetcher_Auto_Loader(), "init"]
    );
}
class_loader_init();

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-tr-news-app-activator.php
 */
function activate_tru_fetcher() {
    (new Tru_Fetcher_Activator())->activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-tr-news-app-deactivator.php
 */
function deactivate_tru_fetcher() {
    (new Tru_Fetcher_Deactivator())->deactivate();
}

register_activation_hook( __FILE__, 'activate_tru_fetcher' );
register_deactivation_hook( __FILE__, 'deactivate_tru_fetcher' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-tru-fetcher.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_tru_fetcher() {

	$plugin = new Tru_Fetcher();
	$plugin->run();

}
run_tru_fetcher();
