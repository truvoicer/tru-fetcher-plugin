<?php

namespace TruFetcher\Includes\Admin\AdminMenu\Menus;

use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu_Constants;
use TruFetcher\Includes\Admin\AdminPages\Tru_Fetcher_Admin_Page_Loader;

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
abstract class Tru_Fetcher_Admin_Menu_Base
{
    abstract function setConfig(array $config);
    abstract function getConfig();

    protected array $config;
    protected array $pageData = [];

    public function loadMenuPage() {
        return (new Tru_Fetcher_Admin_Page_Loader())
            ->setPageData($this->getPageData())
            ->loadPage($this->getConfig()[Tru_Fetcher_Admin_Menu_Constants::$pageKey]);
    }

    /**
     * @return array
     */
    public function getPageData(): array
    {
        return $this->pageData;
    }

    /**
     * @param array $pageData
     */
    public function setPageData(array $pageData): void
    {
        $this->pageData = $pageData;
    }

    /**
     * @param string $key
     * @param array $data
     */
    public function addPageData(string $key, array $data): void
    {
        $this->pageData[$key] = $data;
    }

}
