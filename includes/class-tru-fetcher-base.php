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
class Tru_Fetcher_Base {

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
