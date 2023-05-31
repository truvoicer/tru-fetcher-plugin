<?php

namespace TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus;

use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\Tru_Fetcher_Admin_Menu_Root;
use TruFetcher\Includes\Admin\AdminMenu\Menus\Tru_Fetcher_Admin_Menu_Base;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu;
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
class Tru_Fetcher_Admin_SubMenu_Settings extends Tru_Fetcher_Admin_Menu_Base
{
    public static string $menuSlug = "tru-fetcher-settings";

    public function __construct()
    {
        $this->setConfig([
            Tru_Fetcher_Admin_Menu_Constants::$typeKey => Tru_Fetcher_Admin_Menu::TYPE_WP_SUBMENU_PAGE,
            Tru_Fetcher_Admin_Menu_Constants::$pageKey => 'tru-fetcher-settings-page',
            Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey 	=> 'Tru Fetcher Settings',
            Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey	=> 'Tru Fetcher Settings',
            Tru_Fetcher_Admin_Menu_Constants::$menuSlugKey	=> self::$menuSlug,
            Tru_Fetcher_Admin_Menu_Constants::$slugKey => self::$menuSlug,
            Tru_Fetcher_Admin_Menu_Constants::$parentSlugKey	=> Tru_Fetcher_Admin_Menu_Root::$menuSlug,
            Tru_Fetcher_Admin_Menu_Constants::$iconKey => "dashicons-menu",
            Tru_Fetcher_Admin_Menu_Constants::$capabilityKey => "manage_options",
            Tru_Fetcher_Admin_Menu_Constants::$callbackKey => [$this, "loadMenuPage"],
        ]);
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

}
