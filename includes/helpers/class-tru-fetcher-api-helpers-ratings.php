<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Ratings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Ratings;
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
class Tru_Fetcher_Api_Helpers_Ratings {

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_User;
    const MAX_RATING = 5;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_settings';

    protected Tru_Fetcher_DB_Model_Ratings $ratingsModel;

    private Tru_Fetcher_DB_Repository_Ratings $ratingsRepository;
    private Tru_Fetcher_DB_Engine $db;

    public function __construct()
    {
        $this->ratingsModel = new Tru_Fetcher_DB_Model_Ratings();
        $this->ratingsRepository = new Tru_Fetcher_DB_Repository_Ratings();
        $this->db = new Tru_Fetcher_DB_Engine();
    }


    public function getRatingsData($providerName, $category, $idList, $user_id)
    {
        if (count($idList) === 0) {
            return [];
        }
        $getRatings = [];
        foreach ($idList as $item) {
            $getItemRating = $this->getRatingsRepository()->fetchRating(
                $user_id,
                $item,
                $providerName,
                $category
            );
            if (!$getItemRating) {
                continue;
            }

            $overallRating = $this->getOverallRatingForItem($getItemRating);
            if (is_array($overallRating)) {
                $getItemRating['overall_rating'] = $overallRating["overall_rating"];
                $getItemRating['total_users_rated'] = $overallRating["total_users_rated"];
            }

            $getRatings[] = $getItemRating;

        }
        return $getRatings;
    }
    public function getOverallRatingForItem(array $data)
    {
        $getTotal = $this->ratingsRepository->getTotalUserRating(
            $data['item_id'],
            $data['provider_name'],
            $data['category']
        );
        if (!$getTotal || !isset($getTotal['rating']) || !isset($getTotal['total_users_rated'])) {
            return null;
        }
        $maxUserRatingCount = (int)$getTotal['total_users_rated'] * self::MAX_RATING;
        $calculateRating = ((int)$getTotal['rating'] * self::MAX_RATING) / $maxUserRatingCount;
        $roundUpToInteger = ceil($calculateRating);
        return [
            "overall_rating" => $roundUpToInteger,
            "total_users_rated" => (int)$getTotal['total_users_rated']
        ];
    }

    public function getInsertDataFromRequest(\WP_User $user, \WP_REST_Request $request) {
        $data = $request->get_params();
        $insertData = [];
        $columns = [
            $this->ratingsModel->getItemIdColumn(),
            $this->ratingsModel->getProviderNameColumn(),
            $this->ratingsModel->getCategoryColumn(),
            $this->ratingsModel->getRatingColumn(),
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
        $insertData[$this->ratingsModel->getUserIdColumn()] = $user->ID;
        return $insertData;
    }
    public function saveRating(\WP_REST_Request $request)
    {
        $getInsertData = $this->getInsertDataFromRequest($this->getUser(), $request);
        if (is_wp_error($getInsertData)) {
            return $getInsertData;
        }
        return $this->ratingsRepository->saveRating($this->getUser(), $getInsertData);
    }

    public function updateRating(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (!isset($requestData[$this->ratingsModel->getIdColumn()])) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_missing_id',
                __('Missing ID', 'tru-fetcher'),
            );
        }
        return $this->ratingsRepository->updateRating($this->getUser(), $requestData[$this->ratingsModel->getIdColumn()], $requestData);
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
        return $this->ratingsRepository->deleteBatchRatings($this->getUser(), $data);
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
     * @return Tru_Fetcher_DB_Repository_Ratings
     */
    public function getRatingsRepository(): Tru_Fetcher_DB_Repository_Ratings
    {
        return $this->ratingsRepository;
    }

}
