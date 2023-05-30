<?php

namespace TruFetcher\Includes\Admin\AdminMenu\Menus;

use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\Tru_Fetcher_Admin_Menu_Root;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu_Constants;
use TruFetcher\Includes\Admin\Taxonomies\Tru_Fetcher_Categories_Taxonomy;
use TruFetcher\Includes\Tru_Fetcher;

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
class Tru_Fetcher_Admin_SubMenu_Categories extends Tru_Fetcher_Admin_Menu_Base
{
    public static string $menuSlug = "tr-news-app-categories";

    public function __construct()
    {
        $this->setConfig([
            Tru_Fetcher_Admin_Menu_Constants::$typeKey => Tru_Fetcher_Admin_Menu::TYPE_WP_MENU_TAXONOMY,
            Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey => "News App Categories",
            Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey => "News App Categories",
            Tru_Fetcher_Admin_Menu_Constants::$slugKey => sprintf(
                Tru_Fetcher::TAXONOMY_ENDPOINT,
                Tru_Fetcher_Categories_Taxonomy::TAXONOMY_TR_NEWS_APP_CATEGORIES
            ),
            Tru_Fetcher_Admin_Menu_Constants::$capabilityKey => "manage_options",
            Tru_Fetcher_Admin_Menu_Constants::$parentSlugKey	=> Tru_Fetcher_Admin_Menu_Root::$menuSlug,
            Tru_Fetcher_Admin_Menu_Constants::$iconKey => "dashicons-menu",
        ]);
    }

    public function getMenu()
    {
        return $this;
    }

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getMenuSlug(): string
    {
        return $this->menuSlug;
    }

    /**
     * @param string $menuSlug
     */
    public function setMenuSlug(string $menuSlug): void
    {
        $this->menuSlug = $menuSlug;
    }
}
