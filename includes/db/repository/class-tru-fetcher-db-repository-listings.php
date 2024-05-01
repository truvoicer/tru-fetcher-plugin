<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Form;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Listings;
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
class Tru_Fetcher_DB_Repository_Listings extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Listings());
    }

    public function findById(int $id)
    {
        $listing =  parent::findById($id);
        if (!$listing) {
            return $listing;
        }
        return $listing;
    }


    public function findListings()
    {
        $find = $this->findMany();
        if (!$find) {
            return $find;
        }
        return $find;
    }
    public function findListingByName(string $name)
    {
        $this->addWhere($this->model->getNameColumn(), $name);
        $listing = $this->findOne();
        if (!$listing) {
            return $listing;
        }
        return $listing;
    }

    private function buildListingInsertData(array $requestData)
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
        $configData = $this->escapeString($requestData[$this->model->getConfigDataColumn()]);

        $data[$this->model->getConfigDataColumn()] = $configData;
        return $data;
    }

    public function insertListing($data)
    {
        $listing = $this->buildListingInsertData($data);
        if (!$listing) {
            return false;
        }
        $fetch = $this->findListingByName($listing[$this->model->getNameColumn()]);
        if ($fetch) {
            $this->addError(new \WP_Error('duplicate_error', 'Listing already exists with same name'));
            return false;
        }
        return $this->insert($listing);
    }

    private function buildListingUpdateData(int $id, array $requestData)
    {
        $data = [];
        $data[$this->model->getIdColumn()] = $id;
        if (!empty($requestData[$this->model->getNameColumn()])) {
            $data[$this->model->getNameColumn()] = $requestData[$this->model->getNameColumn()];
        }
        if (empty($requestData[$this->model->getConfigDataColumn()])) {
            return $data;
        }
        $configData = $this->escapeString($requestData[$this->model->getConfigDataColumn()]);

        $data[$this->model->getConfigDataColumn()] = $configData;
        return $data;
    }
    public function updateListing(int $id, array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $listing = $this->buildListingUpdateData($id, $data);
        if (!$listing) {
            $this->addError(new \WP_Error('update_error', 'Update data is invalid'));
            return false;
        }
        $fetch = $this->findListingByName($listing[$this->model->getNameColumn()]);
        if ($fetch && $fetch[$this->model->getIdColumn()] !== $id) {
            $this->addError(new \WP_Error('duplicate_error', 'Listing already exists with same name'));
            return false;
        }

        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->update($listing);
    }

    public function deleteListing($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
