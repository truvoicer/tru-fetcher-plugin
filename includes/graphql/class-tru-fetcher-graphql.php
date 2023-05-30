<?php
namespace TruFetcher\Includes\GraphQl;

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
        "type", "field", "resolver", "connection"
    ];

    private $listingsClass;
    private $sidebarClass;
    private $postsClass;

    public function __construct()
    {
        $this->listingsClass = new Tru_Fetcher_Listings();
        $this->sidebarClass = new Tru_Fetcher_Sidebars();
        $this->postsClass = new Tru_Fetcher_Posts();
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
