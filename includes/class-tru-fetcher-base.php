<?php
namespace TruFetcher\Includes;
use Exception;

/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

use TruFetcher\Includes\Tru_Fetcher_i18n;

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
class Tru_Fetcher_Base {

    protected $plugin_name;
    private string $reactSecretKey;
    private string $appSecretKey;

    protected $version;

    /**
     * @throws Exception
     */
    public function __construct() {

        if (defined('TRU_FETCHER_VERSION')) {
            $this->version = TRU_FETCHER_VERSION;
        } else {
            $this->version = '1.0.0';
        }
        $this->plugin_name = TRU_FETCHER_PLUGIN_NAME;
        $dotenv = \Dotenv\Dotenv::createImmutable(TRU_FETCHER_PLUGIN_DIR);
        $dotenv->load();
        $this->set_locale();
        $this->setReactSecretKey();
        $this->setAppSecretKey();
    }

    private function set_locale(): void
    {
        $plugin_i18n = new Tru_Fetcher_i18n();
//        add_action('plugins_loaded', $plugin_i18n, ['load_plugin_textdomain']);
    }

    public static function getCurrentUser(): \WP_User
    {
        require_once ABSPATH . WPINC . '/pluggable.php';
        return \wp_get_current_user();
    }

    /**
     * The name of the plugin used to uniquely identify it within the context of
     * WordPress and to define internationalization functionality.
     *
     * @return    string    The name of the plugin.
     * @since     1.0.0
     */
    public function get_plugin_name()
    {
        return $this->plugin_name;
    }

    /**
     * The reference to the class that orchestrates the hooks with the plugin.
     *
     * @return    Tru_Fetcher_Loader    Orchestrates the hooks of the plugin.
     * @since     1.0.0
     */
    public function get_loader()
    {
        return $this->loader;
    }

    /**
     * Retrieve the version number of the plugin.
     *
     * @return    string    The version number of the plugin.
     * @since     1.0.0
     */
    public function get_version()
    {
        return $this->version;
    }

    /**
     * @return string
     */
    public function getReactSecretKey(): string
    {
        return $this->reactSecretKey;
    }

    /**
     * @throws Exception
     */
    public function setReactSecretKey(): void
    {
        if (empty($_ENV['TRU_FETCHER_REACT_SECRET'])) {
            throw new Exception('React secret is invalid');
        }
        $this->reactSecretKey = $_ENV['TRU_FETCHER_REACT_SECRET'];
    }

    /**
     * @return string
     */
    public function getAppSecretKey(): string
    {
        return $this->appSecretKey;
    }

    /**
     * @throws Exception
     */
    public function setAppSecretKey(): void
    {
        if (empty($_ENV['TRU_FETCHER_APP_SECRET'])) {
            throw new Exception('App secret is invalid');
        }
        $this->appSecretKey = $_ENV['TRU_FETCHER_APP_SECRET'];
    }
    public static function getConfig($configName = null, $array = false) {
        if ($configName === null) {
            wp_die("Config name not valid");
        }
        $config = file_get_contents(
            sprintf(plugin_dir_path( dirname( __FILE__ ) ) . '/config/%s.json', $configName)
        );
        if (!$config || $config === null) {
            wp_die(sprintf("Get config failed for (%s).", $configName));
        }
        return json_decode( $config, $array );
    }
}
