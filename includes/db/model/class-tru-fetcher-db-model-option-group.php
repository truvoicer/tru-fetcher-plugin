<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Option_Group extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_option_group';
    public string $tableName = self::TABLE_NAME;

    protected bool $dateInserts = false;
    protected array $tableConfig = [];

    private string $nameColumn = 'name';
    private string $defaultValueColumn = 'default_value';

    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getNameColumn() => 'varchar(512) NOT NULL UNIQUE',
                $this->getDefaultValueColumn() => 'varchar(512) NULL DEFAULT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'optionGroup',
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
     * @param string $nameColumn
     */
    public function setNameColumn(string $nameColumn): void
    {
        $this->nameColumn = $nameColumn;
    }

	/**
	 * @return string
	 */
	public function getDefaultValueColumn(): string
	{
		return $this->defaultValueColumn;
	}

	/**
	 * @param string $defaultValueColumn
	 */
	public function setDefaultValueColumn(string $defaultValueColumn): void
	{
		$this->defaultValueColumn = $defaultValueColumn;
	}

}
