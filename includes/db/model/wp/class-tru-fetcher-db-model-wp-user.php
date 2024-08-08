<?php

namespace TruFetcher\Includes\DB\Model\WP;

use TrfRecruit\Includes\DB\Model\Trf_Recruit_DB_Model_User_Skill;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_WP_User extends Tru_Fetcher_DB_Model_WP
{
    private string $userIdField = 'ID';
    private string $emailField = 'user_email';
    private string $displayNameField = 'display_name';

    public function __construct()
    {
        $this->setConfig([
            parent::FIELDS => [
                $this->getUserIdField(),
                $this->getEmailField(),
                $this->getDisplayNameField(),
            ],
			Tru_Fetcher_DB_Model_Constants::ALIAS => 'user',
			parent::WP_RETURN_DATA_TYPE => \WP_Term::class,
            Tru_Fetcher_DB_Model_Constants::PIVOTS => [
                [
                    Tru_Fetcher_DB_Model_Constants::PIVOTS_TABLE => Trf_Recruit_DB_Model_User_Skill::class,
                    Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_TABLE => self::class,
                    Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY => (new Trf_Recruit_DB_Model_User_Skill())->getSkillIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY_REFERENCE => $this->getUserIdField(),
                    Tru_Fetcher_DB_Model_Constants::PIVOT_RELATED_TABLE => Tru_Fetcher_DB_Model_WP_User::class,
                    Tru_Fetcher_DB_Model_Constants::PIVOT_RELATED_KEY => (new Trf_Recruit_DB_Model_User_Skill())->getUserIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::PIVOT_RELATED_REF => (new Tru_Fetcher_DB_Model_WP_User())->getUserIdField()
                ],
            ]
        ]);
    }

    public function getData(array $foreignKey, array $result)
    {
        return [];
    }

    public function getUserIdField(): string
    {
        return $this->userIdField;
    }

    public function getEmailField(): string
    {
        return $this->emailField;
    }

    public function getDisplayNameField(): string
    {
        return $this->displayNameField;
    }

}
