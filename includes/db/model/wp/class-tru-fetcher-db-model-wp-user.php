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
