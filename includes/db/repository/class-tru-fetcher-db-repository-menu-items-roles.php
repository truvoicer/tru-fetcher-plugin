<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device_Topic;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_MenuItems_Roles;
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
class Tru_Fetcher_DB_Repository_Menu_Items_Roles extends Tru_Fetcher_DB_Repository_Base {

    private Tru_Fetcher_DB_Model_MenuItems_Roles $menuItemsRolesModel;
    public function __construct()
    {
        $this->menuItemsRolesModel = new Tru_Fetcher_DB_Model_MenuItems_Roles();
        parent::__construct($this->menuItemsRolesModel);
    }

    public function fetchMenuItemRoles(int $menuItemId) {
        $this->addWhere($this->menuItemsRolesModel->getMenuItemIdColumn(), $menuItemId);
        return $this->findMany();
    }

    public function buildInsertData(int $menuItemId, string $role) {
        $data = [
            $this->menuItemsRolesModel->getMenuItemIdColumn() => $menuItemId,
            $this->menuItemsRolesModel->getRoleColumn() => $role,
        ];
        return $data;
    }
    public function insertMenuItemRoleBatch(int $menuItemId, array $roles)
    {
        $errors = [];
        foreach ($roles as $role) {
            if (!$this->insertMenuItemRole($menuItemId, $role)) {
                $errors[] = true;
            }
        }
        return count($errors) === 0;
    }
    public function insertMenuItemRole(int $menuItemId, int $role)
    {
        $buildInsertData = $this->buildInsertData($menuItemId, $role);
        if (!$buildInsertData) {
            return false;
        }
        return $this->insert($buildInsertData);
    }

    public function buildUpdateData(int $menuItemId, string $role) {
        $data = [
            $this->menuItemsRolesModel->getMenuItemIdColumn() => $menuItemId,
            $this->menuItemsRolesModel->getRoleColumn() => $role,
        ];
        return $data;
    }
    public function updateMenuItemRoleBatch(int $menuItemId, array $roles)
    {
        $errors = [];
        foreach ($roles as $role) {
            if (!$this->updateMenuItemRole($menuItemId, $role)) {
                $errors[] = true;
            }
        }
        return count($errors) === 0;
    }
    public function updateMenuItemRole(int $id, string $role)
    {
        $buildUpdateData = $this->buildUpdateData($id, $role);
        if (!$buildUpdateData) {
            return false;
        }
        return $this->insert($buildUpdateData);
    }

    public function deleteRoleBatchByMenuItemIdRole(int $menuItemId, array $roles)
    {
        $this->addWhereQueryCondition(
            $this->menuItemsRolesModel->getRoleColumn(),
            Tru_Fetcher_DB_Model_Constants::DATA_TYPE_INT
        );
        $this->addWhereQueryCondition(
            $this->menuItemsRolesModel->getMenuItemIdColumn()
        );

        $deleteData = [];
        foreach ($roles as $deletedRole) {
            $deleteData[$this->menuItemsRolesModel->getRoleColumn()] = $deletedRole;
            $deleteData[$this->menuItemsRolesModel->getMenuItemIdColumn()] = $menuItemId;
        }
        $delete = $this->deleteMenuItemRole(
            $deleteData
        );
        return $delete;
    }
    public function deleteMenuItemRole(array $data)
    {
        return $this->deleteData($data);
    }

    /**
     * @return Tru_Fetcher_DB_Model_MenuItems_Roles
     */
    public function getMenuItemsRolesModel(): Tru_Fetcher_DB_Model_MenuItems_Roles
    {
        return $this->menuItemsRolesModel;
    }

}
