<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Device extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tru_fetcher_device';
    public string $tableName = self::TABLE_NAME;

    private string $userIdColumn = 'user_id';
    private string $registerTokenColumn = 'register_token';
    private string $allowNotificationsColumn = 'allow_notifications';

    protected array $tableConfig = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTableConfig([
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getUserIdColumn() => 'bigint(20) NULL DEFAULT NULL',
                $this->getRegisterTokenColumn() => 'varchar(512) NOT NULL UNIQUE',
                $this->getAllowNotificationsColumn() => 'tinyint(1) NOT NULL DEFAULT 0',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'device',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
        ]);
    }


    /**
     * @return string
     */
    public function getRegisterTokenColumn(): string
    {
        return $this->registerTokenColumn;
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
    public function getAllowNotificationsColumn(): string
    {
        return $this->allowNotificationsColumn;
    }

}
