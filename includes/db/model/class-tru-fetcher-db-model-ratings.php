<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Ratings extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tru_fetcher_ratings';
    public string $tableName = self::TABLE_NAME;

    protected array $tableConfig = [];

    private string $userIdColumn = 'user_id';
    private string $providerNameColumn = 'provider_name';
    private string $categoryColumn = 'category';
    private string $itemIdColumn = 'item_id';
    private string $ratingColumn = 'rating';

    protected bool $dateInserts = true;

    protected array $casts = [
        'rating' => 'integer',
    ];

    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getUserIdColumn() => 'bigint(20) NOT NULL',
                $this->getProviderNameColumn() => 'varchar(255) NOT NULL',
                $this->getCategoryColumn() => 'varchar(255) NOT NULL',
                $this->getItemIdColumn() => 'varchar(255) NOT NULL',
                $this->getRatingColumn() => 'tinyint(1) DEFAULT 0',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'ratings',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn()
        ];
    }

    /**
     * @return string
     */
    public function getUserIdColumn(): string
    {
        return $this->userIdColumn;
    }

    /**
     * @return string
     */
    public function getProviderNameColumn(): string
    {
        return $this->providerNameColumn;
    }

    /**
     * @return string
     */
    public function getCategoryColumn(): string
    {
        return $this->categoryColumn;
    }

    /**
     * @return string
     */
    public function getItemIdColumn(): string
    {
        return $this->itemIdColumn;
    }

    /**
     * @return string
     */
    public function getRatingColumn(): string
    {
        return $this->ratingColumn;
    }

}
