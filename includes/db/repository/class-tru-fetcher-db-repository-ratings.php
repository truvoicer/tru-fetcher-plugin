<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Ratings;

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
class Tru_Fetcher_DB_Repository_Ratings extends Tru_Fetcher_DB_Repository_Base {
    private Tru_Fetcher_DB_Model_Ratings $ratingsModel;
    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Ratings());
        $this->ratingsModel = new Tru_Fetcher_DB_Model_Ratings();
    }

    public function fetchByUser(\WP_User $user)
    {
        $this->addWhere($this->ratingsModel->getUserIdColumn(), $user->ID);
        return $this->findMany();
    }

    public function fetchRating(int $userId, int|string $itemId, array $providerName, string $category) {
        $this->addWhere($this->ratingsModel->getItemIdColumn(), $itemId);
        $this->addWhere($this->ratingsModel->getProviderNameColumn(), $providerName, Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_IN);
        $this->addWhere($this->ratingsModel->getCategoryColumn(), $category);
        $this->addWhere($this->ratingsModel->getUserIdColumn(), $userId);
        return $this->findOne();
    }

    public function getTotalUserRating(int|string $itemId, string $providerName, string $category) {
        $this->setSelect([
            "SUM({$this->ratingsModel->getRatingColumn()}) AS {$this->ratingsModel->getRatingColumn()}",
            "(SELECT count({$this->ratingsModel->getIdColumn()}) 
            FROM {$this->ratingsModel->getTableName()}
            WHERE {$this->ratingsModel->getItemIdColumn()}=%s)
            AS total_users_rated",
        ]);
        $this->values[] = $itemId;
        $this->addWhere($this->ratingsModel->getItemIdColumn(), $itemId);
        $this->addWhere($this->ratingsModel->getProviderNameColumn(), $providerName);
        $this->addWhere($this->ratingsModel->getCategoryColumn(), $category);
        return $this->findOne();
    }
    public function buildInsertData(\WP_User $user, array $data) {
        $requiredCols = [
            $this->ratingsModel->getProviderNameColumn(),
            $this->ratingsModel->getCategoryColumn(),
            $this->ratingsModel->getItemIdColumn(),
            $this->ratingsModel->getRatingColumn(),
        ];
        $insertData = [];
        foreach ($requiredCols as $col) {
            if (!isset($data[$col])) {
                return false;
            } else {
                $insertData[$col] = $data[$col];
            }
        }
        $insertData[$this->ratingsModel->getUserIdColumn()] = $user->ID;
        return $insertData;
    }
    public function saveRating(\WP_User $user, array $data)
    {
        $buildInsertData = $this->buildInsertData($user, $data);
        if (!$buildInsertData) {
            return false;
        }

        $this->addWhere($this->ratingsModel->getUserIdColumn(), $buildInsertData[$this->ratingsModel->getUserIdColumn()]);
        $this->addWhere($this->ratingsModel->getProviderNameColumn(), $buildInsertData[$this->ratingsModel->getProviderNameColumn()]);
        $this->addWhere($this->ratingsModel->getCategoryColumn(), $buildInsertData[$this->ratingsModel->getCategoryColumn()]);
        $this->addWhere($this->ratingsModel->getItemIdColumn(), $buildInsertData[$this->ratingsModel->getItemIdColumn()]);
        $findSavedItem = $this->findOne();
        if ($findSavedItem) {
            return $this->updateRating($user, $findSavedItem[$this->ratingsModel->getIdColumn()], $data);
        }
        return $this->insert($buildInsertData);
    }

    public function buildUpdateData(int $id, array $data) {
        $columns = [
            $this->ratingsModel->getRatingColumn(),
//            $this->ratingsModel->getProviderNameColumn(),
//            $this->ratingsModel->getCategoryColumn(),
//            $this->ratingsModel->getItemIdColumn(),
        ];

        $updateData = [];
        foreach ($columns as $col) {
            if (isset($data[$col])) {
                $updateData[$col] = $data[$col];
            }
        }
        $updateData[$this->ratingsModel->getIdColumn()] = $id;

        return $updateData;
    }
    public function updateRating(\WP_User $user, int $id, array $data)
    {
        $buildUpdateData = $this->buildUpdateData($id, $data);
        if (!$buildUpdateData) {
            return false;
        }

        $this->addWhere($this->ratingsModel->getIdColumn(), $id);
        $this->addWhere($this->ratingsModel->getUserIdColumn(), $user->ID);
        $findSavedItem = $this->findOne();
        if ($findSavedItem) {
            $this->setWhereQueryConditions($this->defaultWhereConditions());
            return $this->update($buildUpdateData);
        }
        return false;
    }

    public function deleteBatchRatings(\WP_User $user, array $data) {
        $errors = [];
        foreach ($data as $item) {
            if (!$this->deleteRatingById($user, $item['id'])) {
                $this->addError(
                    new \WP_Error(
                        'delete_rating_error',
                        sprintf(
                            'Failed to delete rating with id %s',
                            $item['id']
                        )
                    )
                );
                $errors[] = true;
            }
        }
        return count($errors) === 0;
    }

    public function deleteRatingById(\WP_User $user, int $id) {
        $this->addWhere($this->ratingsModel->getIdColumn(), $id);
        $this->addWhere($this->ratingsModel->getUserIdColumn(), $user->ID);
        $findSavedItem = $this->findOne();
        if ($findSavedItem) {
            return $this->delete();
        }
        return false;
    }
}
