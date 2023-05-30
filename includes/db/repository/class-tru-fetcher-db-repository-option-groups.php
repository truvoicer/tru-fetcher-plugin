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
class Tru_Fetcher_DB_Repository_Option_Groups extends Tru_Fetcher_DB_Repository_Base
{

    protected Tru_Fetcher_DB_Model_Option_Group_Items $optionGroupItemsModel;

    protected Tru_Fetcher_DB_Repository_Option_Group_Items $optionGroupItemsRepository;

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Option_Group());
        $this->optionGroupItemsModel = new Tru_Fetcher_DB_Model_Option_Group_Items();
        $this->optionGroupItemsRepository = new Tru_Fetcher_DB_Repository_Option_Group_Items();
        $this->optionGroupItemsRepository->setSite($this->getSite());
    }

    public function findOptionGroups(?array $conditions = [])
    {
        $results = $this->db->getAllResults(
            $this->model,
            ARRAY_A
        );
        $results = $this->model->buildModelDataBatch($results);

        return $this->optionGroupItemsRepository->findOptionGroupItemsForBatch($results);
    }

    public function findOptionGroupByName(string $optionGroupName)
    {
        $findOptionGroup = $this->db->getSingleResult(
            $this->model,
            "{$this->model->getNameColumn()} = %s",
            [$optionGroupName],
            ARRAY_A
        );

        if (!$findOptionGroup) {
            return false;
        }
        $results = $this->optionGroupItemsModel->buildModelData($findOptionGroup);

        if (!isset($results[$this->model->getIdColumn()])) {
            return $results;
        }
        $results['optionGroupItems'] = $this->optionGroupItemsRepository->findOptionGroupItems($results[$this->model->getIdColumn()]);
        return $results;
    }

    public function findOptionGroupById(int $optionGroupId)
    {
        $findOptionGroup = $this->db->getSingleResult(
            $this->model,
            "{$this->model->getIdColumn()} = %d",
            [$optionGroupId],
            ARRAY_A
        );

        if (!$findOptionGroup) {
            return false;
        }
        $results = $this->optionGroupItemsModel->buildModelData($findOptionGroup);

        if (!isset($results[$this->model->getIdColumn()])) {
            return $results;
        }
        $results['optionGroupItems'] = $this->optionGroupItemsRepository->findOptionGroupItems($results[$this->model->getIdColumn()]);
        return $results;
    }

    public function insertOptionGroupData($data)
    {
        $optionGroupItem = $this->optionGroupItemsRepository->buildInsertOptionGroupItem($data);
        if (!$optionGroupItem) {
            return false;
        }
        $fetchOptionGroup = $this->findOptionGroupByName($optionGroupItem[$this->model->getNameColumn()]);
        if ($fetchOptionGroup) {
            $this->addError(new \WP_Error('duplicate_error', 'Option group already exists with same name'));
            return false;
        }
        $results = $this->insert($optionGroupItem);
        return $results;
    }

    public function updateOptionGroupData($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $optionGroupItem = $this->optionGroupItemsRepository->buildUpdateOptionGroupItem($data);
        if (!$optionGroupItem) {
            return false;
        }

        $results = $this->update($optionGroupItem);
        return $results;
    }
    private function saveBatchOptionGroups(array $data, string $dbOperation)
    {
        foreach ($data as $menuData) {
            switch ($dbOperation) {
                case $this->db::DB_OPERATION_INSERT:
                    $this->insertOptionGroupData($menuData);
                    break;
                case $this->db::DB_OPERATION_UPDATE:
                    $this->updateOptionGroupData($menuData);
                    break;
            }
        }
    }


    public function createOptionGroupBatch(array $data)
    {
//		$this->clearOptionGroupTables();
        return $this->saveBatchOptionGroups($data, $this->db::DB_OPERATION_INSERT);
    }

    public function updateOptionGroupBatch(array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->saveBatchOptionGroups($data, $this->db::DB_OPERATION_UPDATE);
    }
    public function deleteOptionGroups($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
