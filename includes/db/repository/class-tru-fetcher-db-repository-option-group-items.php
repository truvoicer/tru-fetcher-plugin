<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Option_Group;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Option_Group_Items;

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
class Tru_Fetcher_DB_Repository_Option_Group_Items extends Tru_Fetcher_DB_Repository_Base
{
    public const OPTION_GROUP_ITEMS_REQUEST_KEY = 'optionGroupItems';

    protected Tru_Fetcher_DB_Model_Option_Group $optionGroupModel;

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Option_Group_Items());
        $this->optionGroupModel = new Tru_Fetcher_DB_Model_Option_Group();
    }

    public function findOptionGroupItemsForBatch($data)
    {
        return array_map(function ($item) {
            $item['optionGroupItems'] = $this->findOptionGroupItems($item[$this->optionGroupModel->getIdColumn()]);
            return $item;
        }, $data);
    }

    public function findOptionGroupItemByParams(array $params)
    {
        $results = $this->db->find(
            $this->model,
            $params,
            ARRAY_A
        );
        if (!$results) {
            return false;
        }
        return $this->model->buildModelData($results);
    }

    public function findOptionGroupItems(int $optionGroupId)
    {
        $results = $this->db->getResults(
            $this->model,
            "{$this->model->getOptionGroupIdColumn()} = %d",
            [$optionGroupId],
            ARRAY_A
        );
        if (!$results) {
            return false;
        }
        return $this->model->buildModelDataBatch($results);
    }

    public function buildUpdateOptionGroupItem(array $requestData)
    {
        $optionGroupModelPk = $this->optionGroupModel->getPrimaryKey();
        if (!isset($requestData[$optionGroupModelPk])) {
            return false;
        }
        if (!isset($requestData[self::OPTION_GROUP_ITEMS_REQUEST_KEY]) || !is_array($requestData[self::OPTION_GROUP_ITEMS_REQUEST_KEY])) {
            return $requestData;
        }
        unset($requestData[self::OPTION_GROUP_ITEMS_REQUEST_KEY]);
        return $requestData;
    }

    public function buildInsertOptionGroupItem(array $requestData)
    {
        if (!isset($requestData[self::OPTION_GROUP_ITEMS_REQUEST_KEY]) || !is_array($requestData[self::OPTION_GROUP_ITEMS_REQUEST_KEY])) {
            return $requestData;
        }
//		$optionGroupItems = $requestData[self::OPTION_GROUP_ITEMS_REQUEST_KEY];
//		unset($requestData[self::OPTION_GROUP_ITEMS_REQUEST_KEY]);
//		$requestData[Tru_Fetcher_DB_Model_Constants::RELATIONS_KEY] = [];
//		foreach ($optionGroupItems as $item) {
//			$relationItem = [
//				Tru_Fetcher_DB_Model_Constants::USE_DEPENDENCIES => true,
//				Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $this->model,
//				Tru_Fetcher_DB_Model_Constants::RELATIONS_DB_OPERATION => Tru_Fetcher_DB_Model_Constants::RELATIONS_DB_OPERATION_INSERT,
//			];
//			$fields = [
//				$this->model->getOptionKeyColumn() => $item[$this->model->getOptionKeyColumn()],
//				$this->model->getOptionValueColumn() => $item[$this->model->getOptionValueColumn()],
//				$this->model->getOptionTextColumn() => $item[$this->model->getOptionTextColumn()],
//			];
//			$relationItem[Tru_Fetcher_DB_Model_Constants::FIELDS_KEY] = $fields;
//			$requestData[Tru_Fetcher_DB_Model_Constants::RELATIONS_KEY][] = $relationItem;
//		}
        return $requestData;
    }

    public function insertOptionGroupItemData($data)
    {
        $optionGroupItem = $this->buildInsertOptionGroupItem($data);
        if (!$optionGroupItem) {
            return false;
        }
        $results = $this->insert($optionGroupItem);

        return $results;
    }

    public function updateOptionGroupItemData($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $optionGroupItem = $this->buildUpdateOptionGroupItem($data);
        if (!$optionGroupItem) {
            return false;
        }
        return $this->update($optionGroupItem);
    }

    private function saveBatchOptionGroupItems(array $data, string $dbOperation)
    {
        foreach ($data as $menuData) {
            switch ($dbOperation) {
                case $this->db::DB_OPERATION_INSERT:
                    $this->insertOptionGroupItemData($menuData);
                    break;
                case $this->db::DB_OPERATION_UPDATE:
                    $this->insertOptionGroupItemData($menuData);
                    break;
            }
        }
    }


    public function deleteOptionGroupItems($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
