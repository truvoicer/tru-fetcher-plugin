<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Topic extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_topic';

    const DEFAULT_TOPIC = 'all';

    public string $tableName = self::TABLE_NAME;

    private string $topicNameColumn = 'topic_name';

    protected array $tableConfig = [];

    public function __construct()
    {
        parent::__construct();
        $this->setTableConfig([
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getTopicNameColumn() => 'varchar(512) NOT NULL UNIQUE',
            ],
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'topics',
        ]);
    }

    /**
     * @return string
     */
    public function getTopicNameColumn(): string
    {
        return $this->topicNameColumn;
    }

    /**
     * @param string $topicNameColumn
     */
    public function setTopicNameColumn(string $topicNameColumn): void
    {
        $this->topicNameColumn = $topicNameColumn;
    }

}
