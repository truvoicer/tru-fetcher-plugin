<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Skill;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Form_Presets;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

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
class Tru_Fetcher_DB_Repository_Skill extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Skill());
    }

    public function findSkills()
    {
       return $this->findMany();
    }

    public function findSkillByName(string $name)
    {
        $this->addWhere($this->model->getNameColumn(), $name);
        return $this->findOne();
    }


    public function findSkillByNameOrLabel(?string $name = null, ?string $label = null)
    {
        if ($name) {
            $this->addWhere($this->model->getNameColumn(), $name);
        }
        if ($label) {
            $this->addWhere($this->model->getLabelColumn(), $label);
        }
        return $this->findOne();
    }

    private function buildSkillsData(array $requestData)
    {
        $data = [];
        if (
            !empty($requestData[$this->model->getNameColumn()]) &&
            !empty($requestData[$this->model->getLabelColumn()])
        ) {
            $name = $requestData[$this->model->getNameColumn()];
            $label = $requestData[$this->model->getLabelColumn()];
        } elseif (!empty($data[$this->model->getLabelColumn()])) {
            $name = Tru_Fetcher_Helpers::toSnakeCase($data[$this->model->getLabelColumn()]);
            $label = $requestData[$this->model->getLabelColumn()];
        } elseif (!empty($data[$this->model->getNameColumn()])) {
            $label = $requestData[$this->model->getNameColumn()];
            $name = $requestData[$this->model->getNameColumn()];
        } else {
            $this->addError(new \WP_Error('missing_name', 'Missing name'));
            return false;
        }
        $data[$this->model->getNameColumn()] = $name;
        $data[$this->model->getLabelColumn()] = $label;
        return $data;
    }

    public function insertSkills($data)
    {
        $skills = $this->buildSkillsData($data);
        if (!$skills) {
            return false;
        }
        $fetch = $this->findSkillByName($skills[$this->model->getNameColumn()]);
        if ($fetch) {
            $this->addError(new \WP_Error('duplicate_error', 'Skill already exists with same name'));
            return false;
        }
        return $this->insert($skills);
    }

    private function buildSkillsUpdateData(int $id, array $requestData)
    {
        $skills = $this->buildSkillsData($requestData);
        if (!$skills) {
            return false;
        }
        $skills[$this->model->getIdColumn()] = $id;
        return $skills;
    }
    public function updateSkills(int $id, array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $skills = $this->buildSkillsUpdateData($id, $data);
        if (!$skills) {
            $this->addError(new \WP_Error('update_error', 'Update data is invalid'));
            return false;
        }
        $fetch = $this->findSkillByName($skills[$this->model->getNameColumn()]);
        if ($fetch && $fetch[$this->model->getIdColumn()] !== $id) {
            $this->addError(new \WP_Error('duplicate_error', 'Skill already exists with same name'));
            return false;
        }

        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->update($skills);
    }

    public function deleteSkills($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
