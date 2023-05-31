<?php

namespace TruFetcher\Includes\Admin\AdminPages;

use TruFetcher\Includes\Tru_Fetcher_Helpers;

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
class Tru_Fetcher_Admin_Page_Loader {
    const PAGE_ACTIONS_CONFIG_KEY = 'page_actions_config';
    const PAGE_DATA_CONFIG_KEY = 'page_data_config';
    const LOCALIZED_DATA_CONFIG_KEY = 'localized_data_config';

    private array $pageData;
    private string $pagesPath;

    public function __construct() {
        $this->setPagesPath(plugin_dir_path(__FILE__).'pages');
    }
    public function loadPage(string $pageName) {
//        $this->scriptsInit();
        $path = "{$this->pagesPath}/{$pageName}/index.php";

        if (!file_exists($path)) {
            echo "Page not found: {$path}";
            return false;
        }
        return require_once($path);
    }

    private function scriptsInit() {
        if (!isset($this->pageData[self::LOCALIZED_DATA_CONFIG_KEY])) {
            return;
        }
        wp_localize_script(
            TRU_FETCHER_PLUGIN_NAME,
            'tr_news_app',
            [
                'plugin_url' => TRU_FETCHER_PLUGIN_URL,
                'plugin_admin_url' => TRU_FETCHER_PLUGIN_ADMIN_URL,
//                'admin_ajax_url' => TR_NEWS_APP_ADMIN_PLUGIN_URL,
//                'ajax_url' => TR_NEWS_APP_ADMIN_PLUGIN_URL,
            ]
        );
        foreach ($this->pageData[self::LOCALIZED_DATA_CONFIG_KEY] as $key => $script) {
            wp_localize_script(
                TRU_FETCHER_PLUGIN_NAME,
                Tru_Fetcher_Helpers::toSnakeCase(TRU_FETCHER_PLUGIN_NAME."_".$key),
                $script
            );
        }
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
     * @return Tru_Fetcher_Admin_Page_Loader
     */
    public function setPageData(array $pageData): self
    {
        $this->pageData = $pageData;
        return $this;
    }

    /**
     * @return string
     */
    public function getPagesPath(): string
    {
        return $this->pagesPath;
    }

    /**
     * @param string $pagesPath
     */
    public function setPagesPath(string $pagesPath): void
    {
        $this->pagesPath = $pagesPath;
    }

}
