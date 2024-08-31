<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Ratings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Saved_Items;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Ratings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Saved_Items;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Settings;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_User;

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
class Tru_Fetcher_Api_Helpers_Saved_Items {

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_User;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_settings';

    protected Tru_Fetcher_DB_Model_Saved_Items $savedItemsModel;

    private Tru_Fetcher_DB_Repository_Saved_Items $savedItemsRepository;
    private Tru_Fetcher_DB_Engine $db;

    public function __construct()
    {
        $this->savedItemsModel = new Tru_Fetcher_DB_Model_Saved_Items();
        $this->savedItemsRepository = new Tru_Fetcher_DB_Repository_Saved_Items();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function findUserSavedItems(\WP_User $user, ?array $data = []) {
        $this->savedItemsRepository->addWhere(
            $this->savedItemsModel->getUserIdColumn(),
            $user->ID
        );
        if (isset($data['provider'])) {
            $this->savedItemsRepository->addWhere(
                $this->savedItemsModel->getProviderNameColumn(),
                $data['provider']
            );
        }
        if (isset($data['category'])) {
            $this->savedItemsRepository->addWhere(
                $this->savedItemsModel->getCategoryColumn(),
                $data['category']
            );
        }
        return $this->savedItemsRepository->findMany();
    }
    public function getInsertDataFromRequest(\WP_User $user, \WP_REST_Request $request) {
        $data = $request->get_params();
        $insertData = [];
        $columns = [
            $this->savedItemsModel->getItemIdColumn(),
            $this->savedItemsModel->getProviderNameColumn(),
            $this->savedItemsModel->getCategoryColumn(),
        ];
        foreach ($columns as $column) {
            if (!isset($data[$column])) {
                return new \WP_Error(
                    self::ERROR_PREFIX . '_missing_' . $column,
                    __('Missing ' . $column, 'tru-fetcher'),
                );
            }
        }
        foreach ($columns as $column) {
            $val = $request->get_param($column);
            if ($val) {
                $insertData[$column] = $val;
            }
        }
        return $insertData;
    }

    public function saveItem(\WP_REST_Request $request)
    {
        $getInsertData = $this->getInsertDataFromRequest($this->getUser(), $request);
        if (is_wp_error($getInsertData)) {
            return $getInsertData;
        }
        $findSavedItem = $this->savedItemsRepository->fetchByItemId(
            $this->getUser(),
            $getInsertData[$this->savedItemsModel->getItemIdColumn()],
            [$getInsertData[$this->savedItemsModel->getProviderNameColumn()]],
            $getInsertData[$this->savedItemsModel->getCategoryColumn()],
        );
        if ($findSavedItem) {
            return $this->savedItemsRepository->deleteSavedItemById(
                $this->getUser(),
                $findSavedItem[$this->savedItemsModel->getIdColumn()]
            );
        }
        return $this->savedItemsRepository->insertSavedItem($this->getUser(), $getInsertData);
    }

    public function updateRating(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (!isset($requestData[$this->savedItemsModel->getIdColumn()])) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_missing_id',
                __('Missing ID', 'tru-fetcher'),
            );
        }
        return $this->savedItemsRepository->updateSavedItem($this->getUser(), $requestData[$this->savedItemsModel->getIdColumn()], $requestData);
    }

    public function deleteRating(\WP_REST_Request $request)
    {
        $data = $request->get_params();
        if (
            !isset($data) ||
            !is_array($data)
        ) {
            return false;
        }
        return $this->savedItemsRepository->deleteBatchSavedItems($this->getUser(), $data);
    }

    /**
     * @return Tru_Fetcher_DB_Engine
     */
    public function getDb(): Tru_Fetcher_DB_Engine
    {
        return $this->db;
    }

    /**
     * @param Tru_Fetcher_DB_Engine $db
     */
    public function setDb(Tru_Fetcher_DB_Engine $db): void
    {
        $this->db = $db;
    }

    public function setSite(?\WP_Site $site): void
    {
        $this->site = $site;
        $this->db->setSite($site);
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Saved_Items
     */
    public function getSavedItemsRepository(): Tru_Fetcher_DB_Repository_Saved_Items
    {
        return $this->savedItemsRepository;
    }

}
