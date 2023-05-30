<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_User_Device_Access extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_user_device_access';
    public string $tableName = self::TABLE_NAME;

    private string $userIdColumn = 'user_id';
    private string $deviceIdColumn = 'device_id';

    protected array $tableConfig = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTableConfig([
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getUserIdColumn() => 'bigint(20) NULL DEFAULT NULL',
                $this->getDeviceIdColumn() => 'bigint(20) NULL DEFAULT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'device',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
        ]);
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
    public function getDeviceIdColumn(): string
    {
        return $this->deviceIdColumn;
    }

}
