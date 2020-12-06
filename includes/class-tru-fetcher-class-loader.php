<?php

/**
 * Register all actions and filters for the plugin
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

/**
 * Register all actions and filters for the plugin.
 *
 * Maintain a list of all hooks that are registered throughout
 * the plugin, and register them with the WordPress API. Call the
 * run function to execute the list of actions and filters.
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_Class_Loader {

    public static function loadClassList(array $pathsArray = []) {
        foreach ($pathsArray as $path) {
            self::loadClass($path);
        }
    }

	public static function loadClass(string $path) {
        if (!class_exists(self::getClassNameFromPath($path))) {
            require_once TRU_FETCHER_PLUGIN_DIR . $path;
        }
    }

    public static function getClassNameFromPath(string $path) {
        $splitPath = explode("/", $path);
        $fileName = $splitPath[count($splitPath) - 1];
        $removeClassPrefix = ltrim(rtrim($fileName, ".php"), "class-");

        $splitFilename = explode("-", $removeClassPrefix);
        $makeCamelCase = array_map(function ($value) {
            return ucfirst($value);
        }, $splitFilename);

        return implode("", $makeCamelCase);
    }
}
