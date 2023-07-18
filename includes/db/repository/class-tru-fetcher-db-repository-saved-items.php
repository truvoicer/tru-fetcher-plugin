<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Saved_Items;

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
class Tru_Fetcher_DB_Repository_Saved_Items extends Tru_Fetcher_DB_Repository_Base {
    private Tru_Fetcher_DB_Model_Saved_Items $savedItemsModel;
    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Saved_Items());
        $this->savedItemsModel = new Tru_Fetcher_DB_Model_Saved_Items();
    }

    public function fetchByItemIdBatch(\WP_User $user, string $providerName, string $category, array $itemIds)
    {
        $this->addWhere($this->savedItemsModel->getUserIdColumn(), $user->ID);
        $this->addWhere($this->savedItemsModel->getProviderNameColumn(), $providerName);
        $this->addWhere($this->savedItemsModel->getCategoryColumn(), $category);
        $this->addWhere($this->savedItemsModel->getItemIdColumn(), $itemIds, 'IN');
        return $this->findMany();
    }
    public function fetchByUser(\WP_User $user)
    {
        $this->addWhere($this->savedItemsModel->getUserIdColumn(), $user->ID);
        return $this->findMany();
    }

    public function buildInsertData(\WP_User $user, array $data) {
        $requiredCols = [
            $this->savedItemsModel->getProviderNameColumn(),
            $this->savedItemsModel->getCategoryColumn(),
            $this->savedItemsModel->getItemIdColumn(),
        ];
        $insertData = [];
        foreach ($requiredCols as $col) {
            if (!isset($data[$col])) {
                return false;
            } else {
                $insertData[$col] = $data[$col];
            }
        }
        $insertData[$this->savedItemsModel->getUserIdColumn()] = $user->ID;
        return $insertData;
    }
    public function insertSavedItem(\WP_User $user, array $data)
    {
        $buildInsertData = $this->buildInsertData($user, $data);
        if (!$buildInsertData) {
            return false;
        }

        $this->addWhere($this->savedItemsModel->getUserIdColumn(), $buildInsertData[$this->savedItemsModel->getUserIdColumn()]);
        $this->addWhere($this->savedItemsModel->getProviderNameColumn(), $buildInsertData[$this->savedItemsModel->getProviderNameColumn()]);
        $this->addWhere($this->savedItemsModel->getCategoryColumn(), $buildInsertData[$this->savedItemsModel->getCategoryColumn()]);
        $this->addWhere($this->savedItemsModel->getItemIdColumn(), $buildInsertData[$this->savedItemsModel->getItemIdColumn()]);
        $findSavedItem = $this->findOne();
        if ($findSavedItem) {
            return false;
        }
        return $this->insert($buildInsertData);
    }

    public function buildUpdateData(int $id, array $data)
    {
        $columns = [
//            $this->ratingsModel->getProviderNameColumn(),
//            $this->ratingsModel->getCategoryColumn(),
            $this->savedItemsModel->getItemIdColumn(),
        ];

        $updateData = [];
        foreach ($columns as $col) {
            if (isset($data[$col])) {
                $updateData[$col] = $data[$col];
            }
        }
        $updateData[$this->savedItemsModel->getIdColumn()] = $id;
        return $updateData;
    }
    public function updateSavedItem(\WP_User $user, int $id, array $data)
    {
        $buildUpdateData = $this->buildUpdateData($id, $data);
        if (!$buildUpdateData) {
            return false;
        }
        $this->addWhere($this->savedItemsModel->getIdColumn(), $id);
        $this->addWhere($this->savedItemsModel->getUserIdColumn(), $user->ID);
        $findSavedItem = $this->findOne();
        if ($findSavedItem) {
            return $this->update($buildUpdateData);
        }
        return false;
    }

    public function deleteBatchSavedItems(\WP_User $user, array $data) {
        $errors = [];
        foreach ($data as $item) {
            if (!$this->deleteSavedItemById($user, $item['id'])) {
                $this->addError(
                    new \WP_Error(
                        'delete_saved_item_error',
                        sprintf(
                            'Failed to delete saved item with id %s',
                            $item['id']
                        )
                    )
                );
                $errors[] = true;
            }
        }
        return count($errors) === 0;
    }

    public function deleteSavedItemById(\WP_User $user, int $id) {
        $this->addWhere($this->savedItemsModel->getIdColumn(), $id);
        $this->addWhere($this->savedItemsModel->getUserIdColumn(), $user->ID);
        $findSavedItem = $this->findOne();
        if ($findSavedItem) {
            return $this->delete();
        }
        return false;
    }

}
