<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\Admin\Blocks\Resources\Tru_Fetcher_Admin_Blocks_Resources_Listings;
use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Listings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Listings;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;

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
class Tru_Fetcher_Api_Helpers_Listings {

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_tab_preset';

    private Tru_Fetcher_DB_Engine $db;
    protected Tru_Fetcher_DB_Model_Listings $listingsModel;

    private Tru_Fetcher_DB_Repository_Listings $listingsRepository;
    private Tru_Fetcher_Admin_Blocks_Resources_Listings $listingsBlockResource;

    public function __construct()
    {
        $this->listingsModel = new Tru_Fetcher_DB_Model_Listings();
        $this->listingsRepository = new Tru_Fetcher_DB_Repository_Listings();
        $this->listingsBlockResource = new Tru_Fetcher_Admin_Blocks_Resources_Listings();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function getListings() {
       return array_map(function($listing) {
           if (!is_array($listing['config_data'])) {
               return $listing;
           }
           $listing['config_data'] = $this->listingsBlockResource->buildBlockAttributes($listing['config_data']);
           return $listing;
       }, $this->listingsRepository->findListings());
    }
    public function getListingById(int $id) {
        $listing = $this->listingsRepository->findById($id);
        if (!$listing) {
            return false;
        }
        if (!is_array($listing['config_data'])) {
            return $listing;
        }
        $listing['config_data'] = $this->listingsBlockResource->buildBlockAttributes($listing['config_data']);
        return $listing;
    }
    public function getListingByName(string $name) {
        $listing = $this->listingsRepository->findListingByName($name);
        if (!$listing) {
            return false;
        }
        if (!is_array($listing['config_data'])) {
            return $listing;
        }

        $listing['config_data'] = $this->listingsBlockResource->buildBlockAttributes($listing['config_data']);
        return $listing;
    }

    public function createListingFromRequest(\WP_REST_Request $request)
    {
        if (!$this->listingsRepository->insertListing($request->get_params())) {
            $this->errors = array_merge($this->errors, $this->listingsRepository->getErrors());
            return false;
        }
        return true;
    }

    public function updateListingFromRequest(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->listingsModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        if (!$this->listingsRepository->updateListing(
            $requestData[$this->listingsModel->getIdColumn()],
            $request->get_params()
        )) {
            $this->errors = array_merge($this->errors, $this->listingsRepository->getErrors());
            return false;
        }
        return true;
    }

    public function deleteListing(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->listingsModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        return $this->listingsRepository->deleteById($requestData[$this->listingsModel->getIdColumn()]);
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
     * @return Tru_Fetcher_DB_Model_Listings
     */
    public function getListingsModel(): Tru_Fetcher_DB_Model_Listings
    {
        return $this->listingsModel;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Listings
     */
    public function getListingsRepository(): Tru_Fetcher_DB_Repository_Listings
    {
        return $this->listingsRepository;
    }


}
