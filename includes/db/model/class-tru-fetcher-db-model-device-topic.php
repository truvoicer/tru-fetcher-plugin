<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Device_Topic extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_device_topic';
    public string $tableName = self::TABLE_NAME;

    private string $deviceIdColumn = 'device_id';
    private string $topicIdColumn = 'topic_id';

    protected array $tableConfig = [];
    public function __construct()
    {
        parent::__construct();
        $topicModel = new Tru_Fetcher_DB_Model_Topic();
        $deviceModel = new Tru_Fetcher_DB_Model_Device();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getDeviceIdColumn() => 'mediumint(9) NOT NULL',
                $this->getTopicIdColumn() => 'mediumint(9) NOT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'device_topics',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
            Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD => [
                [
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN => $this->getTopicIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE => [
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL => $topicModel,
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN => $topicModel->getIdColumn()
                    ]
                ],
                [
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN => $this->getDeviceIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE => [
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL => $deviceModel,
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN => $deviceModel->getIdColumn()
                    ]
                ]
            ]
        ];
    }

    /**
     * @return string
     */
    public function getDeviceIdColumn(): string
    {
        return $this->deviceIdColumn;
    }

    /**
     * @param string $deviceIdColumn
     */
    public function setDeviceIdColumn(string $deviceIdColumn): void
    {
        $this->deviceIdColumn = $deviceIdColumn;
    }

    /**
     * @return string
     */
    public function getTopicIdColumn(): string
    {
        return $this->topicIdColumn;
    }

    /**
     * @param string $topicIdColumn
     */
    public function setTopicIdColumn(string $topicIdColumn): void
    {
        $this->topicIdColumn = $topicIdColumn;
    }

}
