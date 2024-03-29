<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Settings extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tru_fetcher_settings';
    public string $tableName = self::TABLE_NAME;
    protected bool $dateInserts = false;

    protected array $tableConfig = [];

    private string $nameColumn = 'name';
    private string $valueColumn = 'value';

    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getNameColumn() => 'varchar(50) NOT NULL',
                $this->getValueColumn() => 'mediumtext NOT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'settings',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
        ];
    }


    /**
     * @return string
     */
    public function getNameColumn(): string
    {
        return $this->nameColumn;
    }

    /**
     * @return string
     */
    public function getValueColumn(): string
    {
        return $this->valueColumn;
    }

}
