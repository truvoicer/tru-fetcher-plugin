<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\Api\Auth\Tru_Fetcher_Api_Auth_Admin;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_MenuItems_Roles;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Category;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Menu;
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
class Tru_Fetcher_DB_Repository_Menu_Items extends Tru_Fetcher_DB_Repository_Base
{

    protected Tru_Fetcher_DB_Repository_Category_Options $categoryOptionsRepo;
    protected Tru_Fetcher_DB_Model_Category_Options $categoryOptionsModel;
    protected Tru_Fetcher_DB_Model_Menu $menuModel;
    protected Tru_Fetcher_DB_Model_MenuItems $menuItemsModel;
    protected Tru_Fetcher_DB_Model_MenuItems_Roles $menuItemsRolesModel;
    protected Tru_Fetcher_DB_Repository_Menu_Items_Roles $menuItemsRolesRepo;

    public function __construct()
    {
        $this->menuItemsModel = new Tru_Fetcher_DB_Model_MenuItems();
        parent::__construct($this->menuItemsModel);
        $this->categoryOptionsRepo = new Tru_Fetcher_DB_Repository_Category_Options();
        $this->categoryOptionsModel = $this->categoryOptionsRepo->getCategoryOptionsModel();
        $this->menuModel = new Tru_Fetcher_DB_Model_Menu();
        $this->menuItemsRolesRepo = new Tru_Fetcher_DB_Repository_Menu_Items_Roles();
        $this->menuItemsRolesModel = $this->menuItemsRolesRepo->getMenuItemsRolesModel();
    }

    public function findMenuItemById($menuItemId)
    {
        $findExistingMenuItemOption = $this->db->getSingleResult(
            new Tru_Fetcher_DB_Model_MenuItems(),
            "id=%d",
            [$menuItemId],
            ARRAY_A
        );

        if (is_array($findExistingMenuItemOption) && !empty($findExistingMenuItemOption)) {
            return $findExistingMenuItemOption;
        }
        return false;
    }


    public function findMenuItemsForMenuBatch($data)
    {
        return array_map(function ($item) {
            $item['menuItems'] = $this->findMenuItems($item[$this->menuModel->getIdColumn()]);
            return $item;
        }, $data);
    }

    public function findAllMenuItems()
    {
        $results = $this->db->getAllResults(
            new Tru_Fetcher_DB_Model_MenuItems(),
            ARRAY_A
        );
        if (!$results) {
            return false;
        }
        $results = $this->model->buildModelDataBatch($results);
        return $this->buildMenuItemsRelationsBatch($results);
    }

    public function buildMenuItemsRelationsBatch(array $menuItems) {
        return array_map(function ($item) {
            return $this->buildMenuItemsRelations($item);
        }, $menuItems);
    }
    public function buildMenuItemsRelations(array $menuItem) {
        $menuItemId = $menuItem[$this->menuItemsModel->getPrimaryKey()];
        if (
            $menuItem[$this->menuItemsModel->getCategoryOptionsOverrideColumn()] &&
            is_int($menuItem[$this->menuItemsModel->getCategoryOptionsIdColumn()])
        ) {
            $menuItem['categoryOptions'] = $this->categoryOptionsRepo->findById(
                $menuItem[$this->menuItemsModel->getCategoryOptionsIdColumn()]
            );
        }
        $menuItem['accessControl'] = [];
        $menuItem['accessControl']['roles'] = $this->menuItemsRolesRepo->addWhere(
            $this->menuItemsRolesModel->getMenuItemIdColumn(),
            $menuItemId
        )->findMany();
        return $menuItem;
    }
    public function findMenuItems(int $menuId)
    {
        $results = $this->db->getResults(
            new Tru_Fetcher_DB_Model_MenuItems(),
            "{$this->model->getMenuIdColumn()}=%d",
            [$menuId],
            ARRAY_A
        );
        if (!$results) {
            return false;
        }
        $results = $this->model->buildModelDataBatch($results);
        return $this->buildMenuItemsRelationsBatch($results);
    }

    public function insertMenuItemCategoryOptions($categoryOptions, $termKey)
    {
        return $this->categoryOptionsRepo->insertCategoryOptions($this->db::DB_OPERATION_INSERT, $categoryOptions, $termKey, true);
    }

    public function updateMenuItemCategoryOptions($categoryOptionsId, array $data)
    {
        $findCategoryOption = $this->categoryOptionsRepo->findCategoryOptionsById($categoryOptionsId);
        if (!$findCategoryOption) {
            return false;
        }

        return $this->categoryOptionsRepo->updateNewsAppCategoryOptions(array_merge(
            [$this->categoryOptionsModel->getIdColumn() => $categoryOptionsId],
            $data
        ));
    }

    private function unsetMenuItemCategoryOption(array $menuItem)
    {
        $findMenuItem = $this->findMenuItemById($menuItem[$this->model->getIdColumn()]);
        if (!$findMenuItem) {
            $this->addError(new \WP_Error('fetch_error', 'Menu item not found'));
            return $menuItem;
        }
        $updateMenuItemsCatOptionsData = [
            $this->model->getIdColumn() => $findMenuItem[$this->model->getIdColumn()],
            $this->model->getCategoryOptionsIdColumn() => null
        ];

        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $update = $this->updateMenuItems($updateMenuItemsCatOptionsData, $this->db::DB_OPERATION_UPDATE, false);
        if (!$update) {
            $this->addError(new \WP_Error('update_error', 'Error unsetting category option in menu items'));
            return $findMenuItem;
        }
        $menuItemCatOptIdCol = $this->model->getCategoryOptionsIdColumn();
        if (!isset($findMenuItem[$menuItemCatOptIdCol]) || !$findMenuItem[$menuItemCatOptIdCol]) {
            return $findMenuItem;
        }
        $this->categoryOptionsRepo->deleteNewsAppCategoryOptions([
            [$this->categoryOptionsModel->getIdColumn() => $findMenuItem[$menuItemCatOptIdCol]]
        ]);

        return $findMenuItem;
    }

    public function menuItemsInsertCategoryOptionsHandler(array $data, array $menuItem)
    {
        $updateMenuItemsCatOptionsData = [
            $this->model->getIdColumn() => $menuItem[$this->model->getIdColumn()],
        ];

        if (!Tru_Fetcher_Api_Helpers_Menu::isCategoryOptionsOverride($data)) {
            return $this->unsetMenuItemCategoryOption($menuItem);
        }

        if (!isset($data['categoryOptions']) || !is_array($data['categoryOptions'])) {
            $this->addError(new \WP_Error('invalid_data_error', 'Category Options not set'));
            $menuItem[$this->model->getCategoryOptionsIdColumn()] = null;
            return $menuItem;
        }
        $findMenuItem = $this->findMenuItemById($menuItem[$this->model->getIdColumn()]);
        if (!$findMenuItem) {
            $this->addError(new \WP_Error('fetch_error', 'Menu item not found'));
            return false;
        }

        $categoryOptionsData = $data['categoryOptions'];
        $termKey = Tru_Fetcher_Api_Helpers_Category::getTermKeyFromCategoryOptionsData($categoryOptionsData);
        $categoryOptionsId = $findMenuItem[$this->model->getCategoryOptionsIdColumn()];
        if (!$categoryOptionsId) {
            $results = $this->insertMenuItemCategoryOptions($categoryOptionsData, $termKey);
            if (!$results) {
                $this->addError(new \WP_Error('insert_error', 'Error creating category options'));
                return $menuItem;
            }
        } else {
            $results = $this->updateMenuItemCategoryOptions($categoryOptionsId, $categoryOptionsData);
            if (!$results) {
                $this->addError(new \WP_Error('insert_error', 'Error updating category options'));
                return $menuItem;
            }
        }
        $updateMenuItemsCatOptionsData[$this->model->getCategoryOptionsIdColumn()] = $results[$this->categoryOptionsModel->getIdColumn()];
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $updateMenuItems = $this->updateMenuItems($updateMenuItemsCatOptionsData, $this->db::DB_OPERATION_UPDATE, false);
        if (!$updateMenuItems) {
            return $menuItem;
        }
        $results['categoryOptions'] = $results;
        return $results;
    }


    private function buildMenuItem(array $requestData)
    {
        return $requestData;
    }

    private function clearMenuItemsTables()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$this->model->getTableName()};"));
        $wpdb->query($wpdb->prepare("DELETE FROM {$this->categoryOptionsModel->getTableName()};"));
    }

    public function insertMenuItems($data, ?bool $updateCategoryOptions = true)
    {
        $menuItem = $this->buildMenuItem($data);
        $results = $this->insert($menuItem);
        if (!$results) {
            return false;
        }
        if ($updateCategoryOptions) {
            return $this->menuItemsInsertCategoryOptionsHandler($menuItem, $results);
        }
        return $results;
    }

    public function updateNewsAppMenuItem(array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->updateMenuItems($data, $this->db::DB_OPERATION_UPDATE);
    }

    public function updateMenuItems($data, string $dbOperation, ?bool $updateCategoryOptions = true)
    {
        $menuItem = $this->buildMenuItem($data, $dbOperation);
        $results = $this->update($menuItem);
        if (!$results) {
            return false;
        }
        if ($updateCategoryOptions) {
            return $this->menuItemsInsertCategoryOptionsHandler($menuItem, $results);
        }
        return $results;
    }


    private function saveBatchMenuItems(array $data, string $dbOperation)
    {
        foreach ($data as $menuItem) {
            switch ($dbOperation) {
                case $this->db::DB_OPERATION_INSERT:
                    $this->insertMenuItems($menuItem, $dbOperation);
                    break;
                case $this->db::DB_OPERATION_UPDATE:
                    $this->updateMenuItems($menuItem, $dbOperation);
                    break;
            }
        }
    }


    public function deleteNewsAppMenuItem($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

    public function updateRoles(int $menuItemId, array $data)
    {
        $fetchRoles = $this->menuItemsRolesRepo
            ->addWhere($this->menuItemsRolesModel->getMenuItemIdColumn(), $menuItemId)
            ->findMany();
        $fetchedRolesValues = array_column($fetchRoles, $this->menuItemsRolesModel->getRoleColumn());

        $deletedRoles = array_filter($fetchedRolesValues, function ($role) use ($data) {
            return !in_array($role, $data);
        });
        if (count($deletedRoles) && !$this->menuItemsRolesRepo->deleteRoleBatchByMenuItemIdRole($menuItemId, $deletedRoles)) {
            return false;
        }

        $newRoles = array_filter($data, function ($role) use ($fetchedRolesValues) {
            return !in_array($role, $fetchedRolesValues);
        });


        return $this->menuItemsRolesRepo->updateMenuItemRoleBatch($menuItemId, $newRoles);
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Menu_Items_Roles
     */
    public function getMenuItemsRolesRepo(): Tru_Fetcher_DB_Repository_Menu_Items_Roles
    {
        return $this->menuItemsRolesRepo;
    }

    /**
     * @return Tru_Fetcher_DB_Model_Menu
     */
    public function getMenuModel(): Tru_Fetcher_DB_Model_Menu
    {
        return $this->menuModel;
    }

    /**
     * @return Tru_Fetcher_DB_Model_MenuItems
     */
    public function getMenuItemsModel(): Tru_Fetcher_DB_Model_MenuItems
    {
        return $this->menuItemsModel;
    }

    /**
     * @return Tru_Fetcher_DB_Model_Category_Options
     */
    public function getCategoryOptionsModel(): Tru_Fetcher_DB_Model_Category_Options
    {
        return $this->categoryOptionsModel;
    }

    /**
     * @return Tru_Fetcher_DB_Model_MenuItems_Roles
     */
    public function getMenuItemsRolesModel(): Tru_Fetcher_DB_Model_MenuItems_Roles
    {
        return $this->menuItemsRolesModel;
    }

}
