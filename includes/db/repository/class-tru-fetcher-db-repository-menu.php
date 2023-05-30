<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Category;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Category_Options;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Menu;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_MenuItems;

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
class Tru_Fetcher_DB_Repository_Menu extends Tru_Fetcher_DB_Repository_Base
{
    public const MENUS_REQUEST_KEY = 'menus';
    public const MENU_ITEMS_REQUEST_KEY = 'menuItems';

    protected Tru_Fetcher_Api_Helpers_Category $category;
    protected Tru_Fetcher_DB_Model_Category_Options $categoryOptionsModel;
    protected Tru_Fetcher_DB_Model_MenuItems $menuItemsModel;

    private Tru_Fetcher_DB_Repository_Menu_Items $menuItemsRepository;

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Menu());
        $this->category = new Tru_Fetcher_Api_Helpers_Category();
        $this->categoryOptionsModel = new Tru_Fetcher_DB_Model_Category_Options();
        $this->menuItemsModel = new Tru_Fetcher_DB_Model_MenuItems();
        $this->menuItemsRepository = new Tru_Fetcher_DB_Repository_Menu_Items();
        $this->menuItemsRepository->setSite($this->getSite());
    }

    protected function clearMenuTables()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$this->model->getTableName()};"));
    }

    private function buildUpdateMenu(array $requestData)
    {
        $menuModelPk = $this->model->getPrimaryKey();
        if (!isset($requestData[$menuModelPk])) {
            return false;
        }
        if (!isset($requestData[self::MENU_ITEMS_REQUEST_KEY]) || !is_array($requestData[self::MENU_ITEMS_REQUEST_KEY])) {
            return $requestData;
        }
        unset($requestData[self::MENU_ITEMS_REQUEST_KEY]);
        return $requestData;
    }

    private function buildInsertMenu(array $requestData)
    {
        if (!isset($requestData[self::MENU_ITEMS_REQUEST_KEY]) || !is_array($requestData[self::MENU_ITEMS_REQUEST_KEY])) {
            return $requestData;
        }
        return $requestData;
    }

    public function insertMenus($data)
    {
        $menuItem = $this->buildInsertMenu($data);
        return $this->insert($menuItem);
    }

    private function updateMenus($data)
    {
        $menuItem = $this->buildUpdateMenu($data);
        return $this->update($menuItem);
    }

    private function saveBatchMenus(array $data, string $dbOperation)
    {
        foreach ($data as $menuData) {
            switch ($dbOperation) {
                case $this->db::DB_OPERATION_INSERT:
                    $this->insertMenus($menuData);
                    break;
                case $this->db::DB_OPERATION_UPDATE:
                    $this->updateMenus($menuData);
                    break;
            }
        }
    }


    public function updateNewsAppMenus(array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->updateMenus($data);
    }

    public function findAllMenus()
    {
        $results = $this->db->getAllResults(
            $this->model,
            ARRAY_A
        );
        $results = $this->model->buildModelDataBatch($results);
        return $this->menuItemsRepository->findMenuItemsForMenuBatch($results);
    }

    public function findMenu(int $menuId)
    {
        $results = $this->db->getSingleResult(
            $this->model,
            "{$this->model->getIdColumn()}=%d",
            [$menuId],
            ARRAY_A
        );
        $results = $this->model->buildModelData($results);
        $results['menuItems'] = $this->menuItemsRepository->findMenuItems($results[$this->model->getIdColumn()]);
        return $results;
    }

    public function deleteNewsAppMenus($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
