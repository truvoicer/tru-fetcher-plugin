<?php

namespace TruFetcher\Includes\Admin\AdminMenu;
require_once('/var/www/html/wp-content/plugins/advanced-custom-fields-pro/pro/options-page.php');
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
class Tru_Fetcher_Admin_Menu {
    const TYPE_WP_MENU_PAGE = "TYPE_WP_MENU_PAGE";
    const TYPE_WP_SUBMENU_PAGE = "TYPE_WP_SUBMENU_PAGE";
    const TYPE_WP_MENU_TAXONOMY = "TYPE_WP_MENU_TAXONOMY";
    const TYPE_WP_MENU_POST_TYPE = "TYPE_WP_MENU_POST_TYPE";
    const TYPE_WP_MENU_OPTIONS_PAGE = "TYPE_WP_MENU_OPTIONS_PAGE";
    const TYPE_WP_MENU_ACF_OPTIONS_PAGE = "TYPE_WP_MENU_ACF_OPTIONS_PAGE";

    private Tru_Fetcher_Admin_Menu_Loader $menuConfig;

    public function __construct() {
        $this->menuConfig = new Tru_Fetcher_Admin_Menu_Loader();
    }

    public function init() {
        $this->addAjaxActions();
        add_action( 'admin_menu', [ $this, 'admin_menu_init'] );
        $this->addAcfOptionPages();
    }

    public function addAcfOptionPages() {
        $menus = $this->menuConfig->getMenusAcfOptionsData();
        foreach ($menus as $menu) {
            $this->addWpAcfOptionsPage($menu);
        }
    }

    public function addAjaxActions() {
        foreach ((new Tru_Fetcher_Admin_Menu_Loader())->getMenuPagesActions() as $data) {
            add_action( "wp_ajax_{$data['action']}", $data['callback'] );
        }
    }

	public function admin_menu_init() {
        foreach ($this->menuConfig->getMenus() as $key => $adminMenu) {
            $this->add_admin_menus($adminMenu);
        }
	}


    public function add_admin_menus($menuData) {
        switch ($menuData[Tru_Fetcher_Admin_Menu_Constants::$typeKey]) {
            case self::TYPE_WP_MENU_PAGE:
                $this->addWpMenuPage($menuData);
                break;
            case self::TYPE_WP_MENU_TAXONOMY:
                $this->addWpTaxonomyMenuPage($menuData);
                break;
        }
        if (
            array_key_exists(Tru_Fetcher_Admin_Menu_Constants::$submenusKey, $menuData) &&
            is_array($menuData[Tru_Fetcher_Admin_Menu_Constants::$submenusKey])
        ) {
            $this->addWpSubMenuPage($menuData[Tru_Fetcher_Admin_Menu_Constants::$submenusKey]);
        }
    }

    public function addWpMenuPage($adminMenu) {
        $add = add_menu_page(
            $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey],
            $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey],
            $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$capabilityKey],
            $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$slugKey],
            $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$callbackKey],
            $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$iconKey],
//            $key + 100
        );
//        var_dump($add);
    }
    public function addWpSubMenuPage($subMenus) {
        foreach ($subMenus as $menu) {
            if (!method_exists($menu, 'getConfig')) {
                continue;
            }
            $config = $menu->getConfig();
            if (!is_array($config)) {
                continue;
            }
            $this->add_admin_menus($config);
        }
    }

    public function addWpTaxonomyMenuPage($menu) {
//        Tru_Fetcher::dd($menu);
//        var_dump('addWpTaxonomyMenuPage');
        add_submenu_page(
            $menu[Tru_Fetcher_Admin_Menu_Constants::$parentSlugKey],
            $menu[Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey],
            $menu[Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey],
            $menu[Tru_Fetcher_Admin_Menu_Constants::$capabilityKey],
            $menu[Tru_Fetcher_Admin_Menu_Constants::$slugKey]
        );
    }

    public function addWpAcfOptionsPage($adminMenu) {
//        var_dump($adminMenu[Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey]);
        if( function_exists('acf_add_options_page') ) {
//            var_dump($adminMenu);
            acf_add_options_page(array(
                Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey 	=> $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$pageTitleKey],
                Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey	=> $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$menuTitleKey],
                Tru_Fetcher_Admin_Menu_Constants::$parentSlugKey	=> $adminMenu[Tru_Fetcher_Admin_Menu_Constants::$parentSlugKey],
            ));
        }
    }

}
