<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_User_Skill extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tru_fetcher_user_skill';
    public string $tableName = self::TABLE_NAME;
    protected bool $dateInserts = false;

    protected array $tableConfig = [];

    private string $userIdColumn = 'user_id';
    private string $skillIdColumn = 'skill_id';

    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getUserIdColumn() => 'bigint(20) NOT NULL',
                $this->getSkillIdColumn() => 'mediumint(9) NOT NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'userSkill',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
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
    public function getSkillIdColumn(): string
    {
        return $this->skillIdColumn;
    }

}
