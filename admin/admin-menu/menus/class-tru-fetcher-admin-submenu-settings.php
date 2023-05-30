<?php

namespace TruFetcher\Includes\Admin\AdminMenu\Menus;

use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\Tru_Fetcher_Admin_Menu_Root;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu_Constants;

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
class Tru_Fetcher_Admin_SubMenu_Settings extends Tru_Fetcher_Admin_Menu_Base
{
    public static string $menuSlug = "tr-news-app-settings";

    public function __construct()
    {
        $this->setConfig([
            Tru_Fetcher_Admin_Menu_Constants::$typeKey => Tru_Fetcher_Admin_Menu::TYPE_WP_MENU_ACF_OPTIONS_PAGE,
            Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey 	=> 'News App Settings',
            Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey	=> 'News App Settings',
            Tru_Fetcher_Admin_Menu_Constants::$menuSlugKey	=> self::$menuSlug,
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
