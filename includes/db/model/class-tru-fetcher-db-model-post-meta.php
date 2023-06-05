<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Post_Meta extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tru_fetcher_post_meta';
    public string $tableName = self::TABLE_NAME;
    protected bool $dateInserts = false;

    protected array $tableConfig = [];

    private string $postIdColumn = 'post_id';
    private string $metaKeyColumn = 'meta_key';
    private string $metaValueColumn = 'meta_value';

    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getPostIdColumn() => "bigint NOT NULL",
                $this->getMetaKeyColumn() => 'varchar(255) NOT NULL',
                $this->getMetaValueColumn() => 'longtext NOT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'postMeta',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
        ];
    }

    /**
     * @return string
     */
    public function getPostIdColumn(): string
    {
        return $this->postIdColumn;
    }

    /**
     * @return string
     */
    public function getMetaKeyColumn(): string
    {
        return $this->metaKeyColumn;
    }

    /**
     * @return string
     */
    public function getMetaValueColumn(): string
    {
        return $this->metaValueColumn;
    }

}
