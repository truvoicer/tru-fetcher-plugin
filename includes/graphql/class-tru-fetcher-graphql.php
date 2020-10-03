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
class Tru_Fetcher_GraphQl
{
    const RESOLVER_FILES = [
        "type", "field", "resolver"
    ];

    private $listingsClass;
    private $sidebarClass;

    public function __construct()
    {
        $this->loadDependencies();
        $this->listingsClass = new Tru_Fetcher_Listings();
        $this->sidebarClass = new Tru_Fetcher_Sidebars();
    }

    private function loadDependencies()
    {
        require_once plugin_dir_path(dirname(__FILE__)) . 'listings/class-tru-fetcher-listings.php';
        require_once plugin_dir_path(dirname(__FILE__)) . 'sidebars/class-tru-fetcher-sidebars.php';
    }

    public function init()
    {
        foreach (self::RESOLVER_FILES as $file) {
            $this->registerResolvers($file);
        }
    }

    private function registerResolvers($filename)
    {
        $dir = new DirectoryIterator(plugin_dir_path(dirname(__FILE__)) . "graphql/resolvers");
        foreach ($dir as $fileinfo) {
            if (!$fileinfo->isDot()) {
                $fileDir = $fileinfo->getRealPath() . '/' . $filename . ".php";
                if (file_exists($fileDir)) {
                    require_once($fileDir);
                }
            }
        }
    }
}
