<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Tab_Presets;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Form_Presets;

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
class Tru_Fetcher_DB_Repository_Tab_Presets extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Tab_Presets());
    }

    public function findById(int $id)
    {
        $tabPreset =  parent::findById($id);
        if (!$tabPreset) {
            return $tabPreset;
        }
        return $this->buildTabPresetData($tabPreset);
    }


    public function findTabPresets()
    {
        $find = $this->findMany();
        if (!$find) {
            return $find;
        }
        return array_map(function ($tabPreset) {
            return $this->buildTabPresetData($tabPreset);
        }, $find);
    }
    public function findTabPresetByName(string $name)
    {
        $this->addWhere($this->model->getNameColumn(), $name);
        $tabPreset = $this->findOne();
        if (!$tabPreset) {
            return $tabPreset;
        }
        return $this->buildTabPresetData($tabPreset);
    }

    public function buildTabPresetData(array $tabPreset) {
        if (empty($tabPreset[$this->model->getConfigDataColumn()])) {
            return $tabPreset;
        }
        $configData = $tabPreset[$this->model->getConfigDataColumn()];
        if (!is_array($configData)) {
            return $tabPreset;
        }
        if (empty($configData['tabs'])) {
            return $tabPreset;
        }
        if (!is_array($configData['tabs'])) {
            return $tabPreset;
        }
        foreach ($configData['tabs'] as $index => $tab) {
            if (empty($tab['form_block']) || !is_array($tab['form_block'])) {
                continue;
            }
            $formBlock = $tab['form_block'];
            if (!empty($formBlock['presets']) && $formBlock['presets'] !== 'custom') {
                $preset = (new Tru_Fetcher_Api_Helpers_Form_Presets())
                    ->getFormPresetsRepository()
                    ->findById((int)$formBlock['presets']);
                if (!empty($preset['config_data'])) {
                    $tabPreset[$this->model->getConfigDataColumn()]['tabs'][$index]['form_block'] = $preset['config_data'];
                }
            }
        }
        return $tabPreset;
    }

    private function buildTabPresetInsertData(array $requestData)
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
        $configData = serialize($requestData[$this->model->getConfigDataColumn()]);

        $data[$this->model->getConfigDataColumn()] = $configData;
        return $data;
    }

    public function insertTabPreset($data)
    {
        $tabPreset = $this->buildTabPresetInsertData($data);
        if (!$tabPreset) {
            return false;
        }
        $fetch = $this->findTabPresetByName($tabPreset[$this->model->getNameColumn()]);
        if ($fetch) {
            $this->addError(new \WP_Error('duplicate_error', 'Tab preset already exists with same name'));
            return false;
        }
        return $this->insert($tabPreset);
    }

    private function buildTabPresetUpdateData(int $id, array $requestData)
    {
        $data = [];
        $data[$this->model->getIdColumn()] = $id;
        if (!empty($requestData[$this->model->getNameColumn()])) {
            $data[$this->model->getNameColumn()] = $requestData[$this->model->getNameColumn()];
        }
        if (empty($requestData[$this->model->getConfigDataColumn()])) {
            return $data;
        }
        $configData = serialize($requestData[$this->model->getConfigDataColumn()]);

        $data[$this->model->getConfigDataColumn()] = $configData;
        return $data;
    }
    public function updateTabPreset(int $id, array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $tabPreset = $this->buildTabPresetUpdateData($id, $data);
        if (!$tabPreset) {
            $this->addError(new \WP_Error('update_error', 'Update data is invalid'));
            return false;
        }
        $fetch = $this->findTabPresetByName($tabPreset[$this->model->getNameColumn()]);
        if ($fetch && $fetch[$this->model->getIdColumn()] !== $id) {
            $this->addError(new \WP_Error('duplicate_error', 'Tab preset already exists with same name'));
            return false;
        }

        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->update($tabPreset);
    }

    public function deleteTabPreset($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
