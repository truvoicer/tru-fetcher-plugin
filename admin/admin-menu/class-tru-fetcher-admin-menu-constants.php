<?php

namespace TruFetcher\Includes\Admin\AdminMenu;

use \ReflectionClass;

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
class Tru_Fetcher_Admin_Menu_Constants {

    public static string $typeKey = 'type';
    public static string $menuTitleKey = 'menu_title';
    public static string $pageTitleKey = 'page_title';
    public static string $slugKey = 'slug';
    public static string $menuSlugKey = 'menu_slug';
    public static string $parentSlugKey = 'parent_slug';
    public static string $capabilityKey = 'capability';
    public static string $callbackKey = 'callback';
    public static string $iconKey = 'icon';
    public static string $submenusKey = 'submenus';
    public static string $pageKey = 'page';

    public static function getAll() {
        return (new ReflectionClass(new self()))->getProperties();
    }
}
