<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Skill;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_User_Skill;

/**
 * Fired during plugin activation
 *
 * @link       https://truvoicer.co.uk
 * @since      1.0.0
 *
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Tru_Fetcher
 * @subpackage Tru_Fetcher/includes
 * @author     Michael <michael@local.com>
 */
class Tru_Fetcher_DB_Repository_User_Skill extends Tru_Fetcher_DB_Repository_Base {

    private Tru_Fetcher_DB_Model_User_Skill $userSkillModel;
    private Tru_Fetcher_DB_Model_Skill $skillModel;
    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_User_Skill());
        $this->userSkillModel = new Tru_Fetcher_DB_Model_User_Skill();
        $this->skillModel = new Tru_Fetcher_DB_Model_Skill();
    }

    public function findUserSkills()
    {
       return $this->findMany();
    }

    public function findUserSkillsByUser(\WP_User $user)
    {
        $this->setSelect([
            "{$this->userSkillModel->getTableName()}.*",
            "{$this->skillModel->getTableName()}.{$this->skillModel->getIdColumn()} as skill_id",
            "{$this->skillModel->getTableName()}.{$this->skillModel->getNameColumn()}",
            "{$this->skillModel->getTableName()}.{$this->skillModel->getLabelColumn()}",
        ]);
        $this->addJoin(
            'left join',
            $this->skillModel->getTableName(),
            "{$this->skillModel->getTableName()}.{$this->skillModel->getIdColumn()} = {$this->userSkillModel->getTableName()}.{$this->userSkillModel->getSkillIdColumn()}"
        );
        $this->addWhere($this->model->getUserIdColumn(), $user->ID);
        return $this->findMany();
    }

    public function findInverseUserSkills(\WP_User $user, array $userSkillIds = [])
    {
        $this->addWhere(
            "{$this->userSkillModel->getIdColumn()}",
            $userSkillIds,
            Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_NOT_IN,
        );
        $this->addWhere(
            "{$this->userSkillModel->getUserIdColumn()}",
            $user->ID,
        );
        return $this->findMany();
    }

    public function findUserSkillByLabel(\WP_User $user, string $label)
    {
        $name = strtolower(str_replace(" ", "_", $label));
        $this->addJoin(
            'left join',
            $this->skillModel->getTableName(),
            "{$this->skillModel->getTableName()}.{$this->skillModel->getIdColumn()} = {$this->userSkillModel->getTableName()}.{$this->userSkillModel->getSkillIdColumn()}"
        );
        $this->addWhere(
            "{$this->skillModel->getFullColumnName($this->skillModel->getLabelColumn())}",
            $label,
            Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_EQUALS,
            Tru_Fetcher_DB_Model_Constants::WHERE_LOGICAL_OPERATOR_AND,
            $this->skillModel
        );
        $this->addWhere(
            "{$this->skillModel->getFullColumnName($this->skillModel->getNameColumn())}",
            $name,
            Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_EQUALS,
            Tru_Fetcher_DB_Model_Constants::WHERE_LOGICAL_OPERATOR_AND,
            $this->skillModel
        );
        $this->addWhere(
            $this->userSkillModel->getFullColumnName($this->userSkillModel->getUserIdColumn()),
            $user->ID
        );
        return $this->findOne();
    }
    public function findUserSkill(\WP_User $user, int $userSkillId, ?bool $joinSkill = false)
    {
        if ($joinSkill) {
            $this->setSelect([
                "{$this->userSkillModel->getTableName()}.*",
                "{$this->skillModel->getTableName()}.{$this->skillModel->getIdColumn()} as skill_id",
                "{$this->skillModel->getTableName()}.{$this->skillModel->getNameColumn()}",
                "{$this->skillModel->getTableName()}.{$this->skillModel->getLabelColumn()}",
            ]);
            $this->addJoin(
                'left join',
                $this->skillModel->getTableName(),
                "{$this->skillModel->getTableName()}.{$this->skillModel->getIdColumn()} = {$this->userSkillModel->getTableName()}.{$this->userSkillModel->getSkillIdColumn()}"
            );
        }
        $this->addWhere($this->model->getUserIdColumn(), $user->ID);
        $this->addWhere($this->model->getSkillIdColumn(), $userSkillId);
        return $this->findOne();
    }

    private function buildUserSkillsData(\WP_User $user, int $userSkillId)
    {
        $data = [];
        $data[$this->model->getUserIdColumn()] = $user->ID;
        $data[$this->model->getSkillIdColumn()] = $userSkillId;
        return $data;
    }

    public function insertUserSkills(\WP_User $user, int $userSkillId)
    {
        $userSkills = $this->buildUserSkillsData($user, $userSkillId);
        if (!$userSkills) {
            return false;
        }
        $fetch = $this->findUserSkill($user, $userSkillId);
        if ($fetch) {
            $this->addError(new \WP_Error('duplicate_error', 'UserSkill already exists'));
            return false;
        }
        return $this->insert($userSkills);
    }

    public function deleteUserSkillByUser(\WP_User $user)
    {
        $this->addWhere($this->model->getUserIdColumn(), $user->ID);
        return $this->delete();
    }

    public function deleteUserSkills($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
