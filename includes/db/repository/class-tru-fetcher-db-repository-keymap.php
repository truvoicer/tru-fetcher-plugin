<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Form;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Keymap;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Tab_Presets;
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
class Tru_Fetcher_DB_Repository_Keymap extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Keymap());
    }

    public function findById(int $id, ?bool $buildConfigData = true)
    {
        $tabPreset =  parent::findById($id);
        if (!$tabPreset) {
            return $tabPreset;
        }
        return $tabPreset;
    }

    public function findKeymapByServiceId(int $serviceId)
    {
        $this->addWhere($this->model->getServiceIdColumn(), $serviceId);
        $tabPreset = $this->findOne();
        if (!$tabPreset) {
            return $tabPreset;
        }
        return $tabPreset;
    }

    private function getKeymapItem(array $data)
    {
        if (
            array_key_exists('key', $data)
        ) {
            return [
                'key' => $data['key'],
                'post_key' => array_key_exists('post_key', $data) ? $data['post_key'] : '',
                'label' => array_key_exists('label', $data) ? $data['label'] : ''
            ];
        }
        return false;
    }

    private function updatekeymapItem(array $item, array $keymapData)
    {
        $findIndex = array_search($item['key'], array_column($keymapData, 'key'));
        if ($findIndex !== false) {
            $keymapData[$findIndex] = $item;
        } else {
            $keymapData[] = $item;
        }
        return $keymapData;
    }

    private function buildKeymapData(array $insertData, ?array $keymapData = [])
    {
        $buildItem = $this->getKeymapItem($insertData);
        if (is_array($buildItem)) {
            return $this->updatekeymapItem($buildItem, $keymapData);
        }
        if (Tru_Fetcher_Helpers::arrayIsList($insertData)) {
            foreach ($insertData as $item) {
                $buildItem = $this->getKeymapItem($item);
                if (!$buildItem) {
                    continue;
                }
                $keymapData = $this->updatekeymapItem($buildItem, $keymapData);
            }
            return $keymapData;
        }
        return false;
    }

    private function buildInsertData(array $data, ?array $keymapData = [])
    {
        $insertData = [];
        if (!array_key_exists($this->model->getKeymapColumn(), $data)) {
            $this->addError(new \WP_Error('update_error', 'Keymap data is missing'));
            return false;
        }
        if (!is_array($data[$this->model->getKeymapColumn()])) {
            $this->addError(new \WP_Error('update_error', 'Keymap data is invalid'));
            return false;
        }
        $buildKeymapData = $this->buildKeymapData($data[$this->model->getKeymapColumn()], $keymapData);

        if (!$buildKeymapData) {
            return false;
        }
        $insertData[$this->model->getKeymapColumn()] = $this->escapeString(serialize($buildKeymapData));
        return $insertData;
    }


    public function insertKeymap(int $serviceId, array $keymapData)
    {
        $keymap = $this->buildInsertData($keymapData);
        if (!$keymap) {
            $this->addError(new \WP_Error('update_error', 'Update data is invalid'));
            return false;
        }
        $keymap[$this->model->getServiceIdColumn()] = $serviceId;
        return $this->insert($keymap);
    }

    public function updateKeymap(int $id, array $data, ?array $record = [])
    {
        if (!count($record)) {
            $record = $this->findById($id);
            if (!$record) {
                $this->addError(new \WP_Error('update_error', 'Keymap record not found'));
                return false;
            }
        }
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $keymap = $this->buildInsertData($data, $record[$this->model->getKeymapColumn()]);
        if (!$keymap) {
            $this->addError(new \WP_Error('update_error', 'Update data is invalid'));
            return false;
        }

        $keymap[$this->model->getIdColumn()] = $id;
        return $this->update($keymap);
    }

    public function saveKeymap(int $serviceId, array $keymapData)
    {
        $keymap = $this->findKeymapByServiceId($serviceId);
        if ($keymap) {
            return $this->updateKeymap($keymap[$this->model->getIdColumn()], $keymapData, $keymap);
        }
        return $this->insertKeymap($serviceId, $keymapData);
    }

    public function deleteKeymap($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
