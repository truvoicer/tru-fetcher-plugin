<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Keymap extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tru_fetcher_keymap';
    public string $tableName = self::TABLE_NAME;
    protected bool $dateInserts = false;

    protected array $tableConfig = [];

    private string $serviceIdColumn = 'service_id';
    private string $keymapColumn = 'keymap';
    protected array $serializedFields = [
        'keymap'
    ];

    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getServiceIdColumn() => 'mediumint(9) NOT NULL',
                $this->getKeymapColumn() => 'longtext NULL DEFAULT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'keymap',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
        ];
    }

    public function getServiceIdColumn(): string
    {
        return $this->serviceIdColumn;
    }

    public function getKeymapColumn(): string
    {
        return $this->keymapColumn;
    }

}
