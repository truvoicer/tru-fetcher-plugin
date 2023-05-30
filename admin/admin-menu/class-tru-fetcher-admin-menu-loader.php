<?php

namespace TruFetcher\Includes\Admin\AdminMenu;

use TruFetcher\Includes\Admin\AdminMenu\Menus\RootMenu\Tru_Fetcher_Admin_Menu_Root;
use TruFetcher\Includes\Admin\AdminPages\Tru_Fetcher_Admin_Page_Loader;

ini_set('xdebug.var_display_max_depth', 10);
ini_set('xdebug.var_display_max_children', 256);
ini_set('xdebug.var_display_max_data', 1024);
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
class Tru_Fetcher_Admin_Menu_Loader
{

    private string $menusDir = 'admin/includes/admin-menu/menus';
    private array $allAcfData = [];
    private array $allPageData = [];
    private array $allPageActions = [];

    public function __construct()
    {
        $this->loadMenus();
    }

    private function loadMenus()
    {
    }

    public function getMenus()
    {
        return [
            (new Tru_Fetcher_Admin_Menu_Root())->getConfig()
        ];
    }

    public function getMenuPagesActions()
    {
        $this->getMenusPageData();

        foreach ($this->allPageData as $data) {
            if (!isset($data[Tru_Fetcher_Admin_Page_Loader::PAGE_ACTIONS_CONFIG_KEY])) {
                continue;
            }
            if (!is_array($data[Tru_Fetcher_Admin_Page_Loader::PAGE_ACTIONS_CONFIG_KEY])) {
                continue;
            }
            $this->allPageActions = array_merge(
                $this->allPageActions,
                $data[Tru_Fetcher_Admin_Page_Loader::PAGE_ACTIONS_CONFIG_KEY]
            );
        }
        return $this->allPageActions;
    }

    public function getMenusPageData()
    {
        $this->setMenusPageData([
            new Tru_Fetcher_Admin_Menu_Root()
        ]);
        return $this->allPageData;
    }
    public function getMenusAcfOptionsData()
    {
        $this->setAcfMenuOptions([
            new Tru_Fetcher_Admin_Menu_Root()
        ]);
        return $this->allAcfData;
    }

    public function setMenusPageData($menus)
    {
        $menu = $this->menuBuilder(
            $menus,
            [],
            function ($menu) {
                return $menu->getPageData();
            }
        );
        $this->allPageData = $menu;
    }

    public function setAcfMenuOptions($menus)
    {
        $menu = $this->menuBuilder(
            $menus,
            [],
            function ($menu) {
                $config = $menu->getConfig();
                if ($config['type'] === Tru_Fetcher_Admin_Menu::TYPE_WP_MENU_ACF_OPTIONS_PAGE) {
                    return $menu->getConfig();
                }
                return false;
            }
        );
        $this->allAcfData = array_filter($menu, function ($item, $key){
            return $item;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function filterMenu($menus)
    {
    }

    private function menuBuilder($menus, $menuData = [], $filterHandler = null)
    {
        foreach ($menus as $menu) {
            if (!method_exists($menu, 'getConfig')) {
                continue;
            }
            $config = $menu->getConfig();
            if (
                isset($config[Tru_Fetcher_Admin_Menu_Constants::$submenusKey]) &&
                is_array($config[Tru_Fetcher_Admin_Menu_Constants::$submenusKey])
            ) {
                $menuData = array_merge(
                    $menuData,
                    $this->menuBuilder(
                        array_map(function ($subMenu) {
                            $className = get_class($subMenu);
                            return new $className();
                        }, $config[Tru_Fetcher_Admin_Menu_Constants::$submenusKey]),
                        $menuData,
                        $filterHandler
                    )
                );
            }
            if ($filterHandler) {
                $menuData[get_class($menu)] = call_user_func($filterHandler, $menu);
            } else {
                $menuData[get_class($menu)] = $menu;
            }
        }
        return $menuData;
    }
}
