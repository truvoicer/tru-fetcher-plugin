<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\WP\Tru_Fetcher_DB_Model_WP_Term;

class Tru_Fetcher_DB_Model_Api_Tokens extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_api_tokens';
    public string $tableName = self::TABLE_NAME;

    protected array $tableConfig = [];

    private string $userIdColumn = 'user_id';
    private string $typeColumn = 'type';
    private string $tokenColumn = 'token';
    private string $issuedAtColumn = 'issued_at';
    private string $expiresAtColumn = 'expires_at';

    protected bool $dateInserts = false;

    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getUserIdColumn() => 'bigint(20) NOT NULL',
                $this->getTypeColumn() => 'varchar(50) NOT NULL',
                $this->getTokenColumn() => 'varchar(512) NOT NULL',
                $this->getIssuedAtColumn() => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
                $this->getExpiresAtColumn() => "datetime NOT NULL DEFAULT '0000-00-00 00:00:00'",
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'apiTokens',
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
    public function getTypeColumn(): string
    {
        return $this->typeColumn;
    }

    /**
     * @return string
     */
    public function getTokenColumn(): string
    {
        return $this->tokenColumn;
    }

    /**
     * @return string
     */
    public function getIssuedAtColumn(): string
    {
        return $this->issuedAtColumn;
    }

    /**
     * @return string
     */
    public function getExpiresAtColumn(): string
    {
        return $this->expiresAtColumn;
    }

}
