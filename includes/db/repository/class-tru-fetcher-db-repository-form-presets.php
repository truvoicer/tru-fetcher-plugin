<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Form_Presets;

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
class Tru_Fetcher_DB_Repository_Form_Presets extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Form_Presets());
    }

    public function findFormPresets()
    {
        return $this->findMany();
    }
    public function findFormPresetByName(string $name)
    {
        $this->addWhere($this->model->getNameColumn(), $name);
        return $this->findOne();
    }

    private function buildFormPresetInsertData(array $requestData)
    {
        $data = [];
        if (!isset($requestData[$this->model->getNameColumn()])) {
            $this->addError(new \WP_Error('missing_name', 'Missing name'));
            return false;
        }
        $data[$this->model->getNameColumn()] = $requestData[$this->model->getNameColumn()];

        if (empty($requestData[$this->model->getConfigDataColumn()])) {
            return $data;
        }
        $configData = $this->escapeString(serialize($requestData[$this->model->getConfigDataColumn()]));

        $data[$this->model->getConfigDataColumn()] = $configData;
        return $data;
    }

    public function insertFormPreset($data)
    {
        $formPreset = $this->buildFormPresetInsertData($data);
        if (!$formPreset) {
            return false;
        }
        $fetch = $this->findFormPresetByName($formPreset[$this->model->getNameColumn()]);
        if ($fetch) {
            $this->addError(new \WP_Error('duplicate_error', 'Form preset already exists with same name'));
            return false;
        }
        return $this->insert($formPreset);
    }

    private function buildFormPresetUpdateData(int $id, array $requestData)
    {
        $data = [];
        $data[$this->model->getIdColumn()] = $id;
        if (!empty($requestData[$this->model->getNameColumn()])) {
            $data[$this->model->getNameColumn()] = $requestData[$this->model->getNameColumn()];
        }
        if (empty($requestData[$this->model->getConfigDataColumn()])) {
            return $data;
        }
        $configData = $this->escapeString(serialize($requestData[$this->model->getConfigDataColumn()]));

        $data[$this->model->getConfigDataColumn()] = $configData;
        return $data;
    }
    public function updateFormPreset(int $id, array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $formPreset = $this->buildFormPresetUpdateData($id, $data);
        if (!$formPreset) {
            $this->addError(new \WP_Error('update_error', 'Update data is invalid'));
            return false;
        }
        $fetch = $this->findFormPresetByName($formPreset[$this->model->getNameColumn()]);
        if ($fetch && $fetch[$this->model->getIdColumn()] !== $id) {
            $this->addError(new \WP_Error('duplicate_error', 'Form preset already exists with same name'));
            return false;
        }

        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->update($formPreset);
    }

    public function deleteFormPreset($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
