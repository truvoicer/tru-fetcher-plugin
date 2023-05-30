<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Option_Group_Items extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_option_group_items';
    public string $tableName = self::TABLE_NAME;

    protected array $tableConfig = [];
    protected bool $dateInserts = false;

    private string $optionGroupIdColumn = 'option_group_id';
    private string $optionKeyColumn = 'option_key';
    private string $optionValueColumn = 'option_value';
    private string $optionTextColumn = 'option_text';


    public function __construct()
    {
        parent::__construct();
        $optionGroupModel = new Tru_Fetcher_DB_Model_Option_Group();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getOptionGroupIdColumn() => "mediumint(9) NOT NULL",
				$this->getOptionKeyColumn() => 'varchar(100) NOT NULL',
				$this->getOptionValueColumn() => 'varchar(100) NOT NULL',
				$this->getOptionTextColumn() => 'varchar(100) NOT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'optionGroupItems',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
            Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD => [
                [
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN => $this->getOptionGroupIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE => [
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL => $optionGroupModel,
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN => $optionGroupModel->getIdColumn()
                    ]
                ],
            ]
        ];
    }

    /**
     * @return string
     */
    public function getOptionGroupIdColumn(): string
    {
        return $this->optionGroupIdColumn;
    }

    /**
     * @param string $optionGroupIdColumn
     */
    public function setOptionGroupIdColumn(string $optionGroupIdColumn): void
    {
        $this->optionGroupIdColumn = $optionGroupIdColumn;
    }

	/**
	 * @return string
	 */
	public function getOptionKeyColumn(): string
	{
		return $this->optionKeyColumn;
	}

	/**
	 * @param string $optionKeyColumn
	 */
	public function setOptionKeyColumn(string $optionKeyColumn): void
	{
		$this->optionKeyColumn = $optionKeyColumn;
	}

	/**
	 * @return string
	 */
	public function getOptionValueColumn(): string
	{
		return $this->optionValueColumn;
	}

	/**
	 * @param string $optionValueColumn
	 */
	public function setOptionValueColumn(string $optionValueColumn): void
	{
		$this->optionValueColumn = $optionValueColumn;
	}

	/**
	 * @return string
	 */
	public function getOptionTextColumn(): string
	{
		return $this->optionTextColumn;
	}

	/**
	 * @param string $optionTextColumn
	 */
	public function setOptionTextColumn(string $optionTextColumn): void
	{
		$this->optionTextColumn = $optionTextColumn;
	}

}
