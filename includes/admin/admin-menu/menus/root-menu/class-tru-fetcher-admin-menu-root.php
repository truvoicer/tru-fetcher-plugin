<?php

namespace TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu;

use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Category_Templates;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Comparison_Lists;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Filter_Lists;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_General_Item;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_General_Lists;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Item_View_Templates;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Items_List;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Listings_Categories;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Post_Templates;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Settings;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Single_Comparison;
use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\SubMenus\Tru_Fetcher_Admin_SubMenu_Single_item;
use TruFetcher\Includes\Admin\AdminMenu\Menus\Tru_Fetcher_Admin_Menu_Base;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu;
use TruFetcher\Includes\Admin\AdminMenu\Tru_Fetcher_Admin_Menu_Constants;
use TruFetcher\Includes\Admin\AdminPages\Tru_Fetcher_Admin_Page_Loader;
use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;

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
class Tru_Fetcher_Admin_Menu_Root extends Tru_Fetcher_Admin_Menu_Base
{
    public static string $menuSlug = "tru-fetcher";
    private Tru_Fetcher_DB_Engine $database;

    public function __construct()
    {
        $this->database = new Tru_Fetcher_DB_Engine();
        $this->setPageData([
                Tru_Fetcher_Admin_Page_Loader::PAGE_ACTIONS_CONFIG_KEY => [
//                    [
//                        'action' => 'send_message_to_device',
//                        'callback' => [new Tru_Fetcher_Admin_Firebase(), "sendMessageToDevice"],
//                    ],
                ],
            ]
        );
        $this->addLocalizedData();
        $this->addData();
        $this->loadMenus();
    }

    private function addLocalizedData()
    {
        $this->addPageData(
            Tru_Fetcher_Admin_Page_Loader::LOCALIZED_DATA_CONFIG_KEY,
            []
        );
    }

    private function addData()
    {
        global $wpdb;
        if (empty($this->database->getDbTables([new Tru_Fetcher_DB_Model_Topic()]))) {
            return false;
        }
        $this->addPageData(
            Tru_Fetcher_Admin_Page_Loader::PAGE_DATA_CONFIG_KEY,
            [
                "devices" => (new Tru_Fetcher_DB_Engine())->getAllResultsRaw(
                    new Tru_Fetcher_DB_Model_Device()
                ),
                "topics" => (new Tru_Fetcher_DB_Engine())->getAllResultsRaw(
                    new Tru_Fetcher_DB_Model_Topic()
                )
            ]
        );
    }

    public function loadMenus()
    {
        $this->setConfig([
            Tru_Fetcher_Admin_Menu_Constants::$typeKey => Tru_Fetcher_Admin_Menu::TYPE_WP_MENU_PAGE,
            Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey => "Tru Fetcher",
            Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey => "Tru Fetcher",
            Tru_Fetcher_Admin_Menu_Constants::$slugKey => self::$menuSlug,
            Tru_Fetcher_Admin_Menu_Constants::$capabilityKey => "manage_options",
            Tru_Fetcher_Admin_Menu_Constants::$pageKey => 'tru-fetcher-root-page',
            Tru_Fetcher_Admin_Menu_Constants::$callbackKey => [$this, "loadMenuPage"],
            Tru_Fetcher_Admin_Menu_Constants::$iconKey => "dashicons-menu",
            Tru_Fetcher_Admin_Menu_Constants::$submenusKey => [
                new Tru_Fetcher_Admin_SubMenu_Items_List(),
                new Tru_Fetcher_Admin_SubMenu_Single_item(),
                new Tru_Fetcher_Admin_SubMenu_Filter_Lists(),
                new Tru_Fetcher_Admin_SubMenu_Category_Templates(),
                new Tru_Fetcher_Admin_SubMenu_Item_View_Templates(),
                new Tru_Fetcher_Admin_SubMenu_Post_Templates(),
                new Tru_Fetcher_Admin_SubMenu_Listings_Categories(),
//                new Tru_Fetcher_Admin_SubMenu_Settings(),
//                new Tru_Fetcher_Admin_SubMenu_Comparison_Lists(),
//                new Tru_Fetcher_Admin_SubMenu_Single_Comparison(),
//                new Tru_Fetcher_Admin_SubMenu_General_Lists(),
//                new Tru_Fetcher_Admin_SubMenu_General_Item(),
            ]
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
     * @param string $key
     * @param $value
     */
    public function addConfig(string $key, $value): void
    {
        $this->config[$key] = $value;
    }
}
