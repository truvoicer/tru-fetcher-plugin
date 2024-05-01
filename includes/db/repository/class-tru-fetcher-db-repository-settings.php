<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;

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
class Tru_Fetcher_DB_Repository_Settings extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Settings());
    }

    public function findMany()
    {
        return $this->buildData(
            parent::findMany()
        );
    }
    public function findSettings(?array $conditions = [])
    {
        $results = $this->db->getAllResults(
            $this->model,
            ARRAY_A
        );
        return $this->buildData(
            $this->model->buildModelDataBatch($results)
        );
    }

    private function buildData(array $data)
    {
        foreach ($data as $index => $value) {
            if (is_serialized($value[$this->model->getValueColumn()])) {
                $data[$index][$this->model->getValueColumn()] = unserialize(
                    str_replace('\\', '', $value[$this->model->getValueColumn()])
                );
            }
        }
        return $data;
    }
    public function findSettingByName(string $settingName)
    {
        $find = $this->db->getSingleResult(
            $this->model,
            "{$this->model->getNameColumn()} = %s",
            [$settingName],
            ARRAY_A
        );

        if (!$find) {
            return false;
        }
        return $this->model->buildModelData($find);
    }

    public function findSettingById(int $id)
    {
        $find = $this->db->getSingleResult(
            $this->model,
            "{$this->model->getIdColumn()} = %d",
            [$id],
            ARRAY_A
        );

        if (!$find) {
            return false;
        }
        return $this->model->buildModelData($find);
    }

    private function buildInsertSettingItem(array $requestData)
    {
        foreach ($requestData as $key => $value) {
            if (is_array($value)) {
                $requestData[$key] = $this->escapeString(serialize($value));
            }
        }
        return $requestData;
    }

    public function insertSettingsData($data)
    {
        $settingsItem = $this->buildInsertSettingItem($data);
        if (!$settingsItem) {
            return false;
        }
        $fetchSettings = $this->findSettingByName($settingsItem[$this->model->getNameColumn()]);
        if ($fetchSettings) {
            $this->addError(new \WP_Error('duplicate_error', 'Setting already exists with same name'));
            return false;
        }
        return $this->insert($settingsItem);
    }

    public function updateSettingsData($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $settingsItem = $this->buildInsertSettingItem($data);
        if (!$settingsItem) {
            return false;
        }

        return $this->update($settingsItem);
    }
    private function saveBatchSettingsItems(array $data, string $dbOperation)
    {
        foreach ($data as $menuData) {
            switch ($dbOperation) {
                case $this->db::DB_OPERATION_INSERT:
                    $this->insertSettingsData($menuData);
                    break;
                case $this->db::DB_OPERATION_UPDATE:
                    $this->updateSettingsData($menuData);
                    break;
            }
        }
    }

    public function createSettingsBatch(array $data)
    {
        return $this->saveBatchSettingsItems($data, $this->db::DB_OPERATION_INSERT);
    }

    public function updateSettingsBatch(array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->saveBatchSettingsItems($data, $this->db::DB_OPERATION_UPDATE);
    }

    public function deleteSettings($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
