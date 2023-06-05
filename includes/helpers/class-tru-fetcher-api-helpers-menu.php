<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth;
use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;


use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Menu_Items_Roles;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;

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
class Tru_Fetcher_Api_Helpers_Menu
{
    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;

    public const MENUS_REQUEST_KEY = 'menus';
    public const MENU_ITEMS_REQUEST_KEY = 'menuItems';

    protected Tru_Fetcher_Api_Helpers_Category $category;

    protected Tru_Fetcher_DB_Repository_Menu $menuRepository;

    protected Tru_Fetcher_DB_Repository_Menu_Items $menuItemsRepository;

    public function __construct()
    {
        $this->category = new Tru_Fetcher_Api_Helpers_Category();
        $this->menuRepository = new Tru_Fetcher_DB_Repository_Menu();
        $this->menuItemsRepository = new Tru_Fetcher_DB_Repository_Menu_Items();
    }

    public static function buildMenuItemsUpdateData(\WP_REST_Request $request)
    {

        return $request->get_param(self::MENUS_REQUEST_KEY);
    }

    public static function getMenuRequestData(\WP_REST_Request $request)
    {
        return $request->get_param(self::MENUS_REQUEST_KEY);
    }


    public static function getMenuItemRequestData(\WP_REST_Request $request)
    {
        return $request->get_param('menuItems');
    }


    public static function isCategoryOptionsOverride(array $data)
    {
        if (
            (isset($data['categoryOptions']) && $data['category_options_override'] === true) &&
            (
                isset($data['categoryOptions']) &&
                is_array($data['categoryOptions']) &&
                !empty($data['categoryOptions'])
            )
        ) {
            return true;
        }
        return false;
    }

    public function getCategoryOptionsDataFromMenuItems(array $menuItem)
    {
        if (isset($menuItem['categoryOptions']) && is_array($menuItem['categoryOptions'])) {
            return $menuItem['categoryOptions'];
        }
        return [];
    }

    public function saveMenusBatch(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        $errors = [];
        foreach ($data as $menu) {
            if (!isset($menu['state'])) {
                $errors[] = new \WP_Error(
                    'tr_news_app_invalid_state',
                    __("State is invalid", 'tr-news-app'),
                );
                continue;
            }
            $operation = $menu['state'];
            $results = false;
            switch ($operation) {
                case 'create':
                    $results = $this->menuRepository->insertMenus($menu);
                    break;
                case 'update':
                    if (!isset($menu['id'])) {
                        break;
                    }
                    $results = $this->menuRepository->updateNewsAppMenus($menu);
                    break;
                default:
                    $errors[] = new \WP_Error(
                        'tr_news_app_invalid_operation',
                        __("Operation {$operation} not supported", 'tr-news-app'),
                    );
                    break;
            }
            if (!$results) {
                $errors[] = new \WP_Error(
                    "tr_news_app_{$operation}_error",
                    __("Operation {$operation} failed", 'tr-news-app'),
                    $menu
                );
            }
        }
        $this->setErrors($errors);
        return count($errors) === 0;
    }

    public function createMenu(\WP_REST_Request $request)
    {
        return $this->menuRepository->insertMenus($request->get_params());
    }

    public function updateMenu(\WP_REST_Request $request)
    {
        return $this->menuRepository->updateNewsAppMenus($request->get_params());
    }

    public function deleteMenus(\WP_REST_Request $request)
    {
        $data = self::getMenuRequestData($request);
        return $this->menuRepository->deleteNewsAppMenus($data);
    }

    public function createMenuItem(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        $createMenuItems = $this->menuItemsRepository->insertMenuItems($requestData);
        if (!$createMenuItems) {
            return false;
        }

        if (isset($requestData['accessControl']['roles']) && is_array($requestData['accessControl']['roles'])) {
            $insertRoles = $this->menuItemsRepository->getMenuItemsRolesRepo()->insertMenuItemRoleBatch(
                $createMenuItems[$this->menuItemsRepository->getMenuItemsModel()->getPrimaryKey()],
                $requestData['accessControl']['roles']
            );
            if (!$insertRoles) {
                return false;
            }
        }
        return true;
    }

    public function updateMenuItem(\WP_REST_Request $request)
    {
        return $this->menuItemsRepository->updateNewsAppMenuItem($request->get_params());
    }

    public function deleteMenuItems(\WP_REST_Request $request)
    {
        $data = self::getMenuItemRequestData($request);
        return $this->menuItemsRepository->deleteNewsAppMenuItem($data);
    }

    public function updateMenuItemRoles(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        $roles = Tru_Fetcher_Api_Auth::getApiRolesRequestData($request);
        if (!isset($data[$this->menuItemsRepository->getMenuItemsModel()->getPrimaryKey()])) {
            return false;
        }
        return $this->menuItemsRepository->updateRoles(
            $data[$this->menuItemsRepository->getMenuItemsModel()->getPrimaryKey()],
            $roles
        );
    }

    /**
     * @return Tru_Fetcher_DB_Engine
     */
    public function getDb(): Tru_Fetcher_DB_Engine
    {
        return $this->db;
    }

    /**
     * @param Tru_Fetcher_DB_Engine $db
     */
    public function setDb(Tru_Fetcher_DB_Engine $db): void
    {
        $this->db = $db;
    }

    public function setSite(?\WP_Site $site): void
    {
        $this->site = $site;
        $this->db->setSite($site);
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Menu
     */
    public function getMenuRepository(): Tru_Fetcher_DB_Repository_Menu
    {
        return $this->menuRepository;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Menu_Items
     */
    public function getMenuItemsRepository(): Tru_Fetcher_DB_Repository_Menu_Items
    {
        return $this->menuItemsRepository;
    }

}
