<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_MenuItems_Roles extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_menuitems_roles';
    public string $tableName = self::TABLE_NAME;

    protected array $tableConfig = [];

    private string $menuItemIdColumn = 'menu_item_id';
    private string $roleColumn = 'role';

    public function __construct()
    {
        parent::__construct();
        $menuItemsModel = new Tru_Fetcher_DB_Model_MenuItems();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getMenuItemIdColumn() => "mediumint(9) NOT NULL",
                $this->getRoleColumn() => 'varchar(250) NOT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'menuItemRoles',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
            Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD => [
                [
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN => $this->getMenuItemIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE => [
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL => $menuItemsModel,
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN => $menuItemsModel->getIdColumn()
                    ]
                ],
            ]
        ];
    }

    /**
     * @return string
     */
    public function getMenuItemIdColumn(): string
    {
        return $this->menuItemIdColumn;
    }

    /**
     * @return string
     */
    public function getRoleColumn(): string
    {
        return $this->roleColumn;
    }



}
