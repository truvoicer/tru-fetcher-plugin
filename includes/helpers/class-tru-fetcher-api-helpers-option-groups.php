<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Option_Group_Items;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Option_Groups;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;

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
class Tru_Fetcher_Api_Helpers_Option_Groups
{
    use Tru_Fetcher_DB_Traits_WP_Site;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_option_groups';
    public const OPTION_GROUP_REQUEST_KEY = 'optionGroup';
    public const OPTION_GROUP_ITEMS_REQUEST_KEY = 'optionGroupItems';

    private Tru_Fetcher_DB_Repository_Option_Groups $optionGroupRepository;

    private Tru_Fetcher_DB_Repository_Option_Group_Items $optionGroupItemsRepository;

    public function __construct()
    {
        $this->optionGroupRepository = new Tru_Fetcher_DB_Repository_Option_Groups();
        $this->optionGroupItemsRepository = new Tru_Fetcher_DB_Repository_Option_Group_Items();
    }

    public static function getOptionGroupRequestData(\WP_REST_Request $request)
    {
        return $request->get_param(self::OPTION_GROUP_REQUEST_KEY);
    }

    public static function getOptionGroupItemsRequestData(\WP_REST_Request $request)
    {
        return $request->get_param(self::OPTION_GROUP_ITEMS_REQUEST_KEY);
    }


    public function createOptionGroupBatchFromRequest(\WP_REST_Request $request)
    {
        $data = self::getOptionGroupRequestData($request);
        $this->optionGroupRepository->createOptionGroupBatch($data);
        return true;
    }

    public function createSingleOptionGroupFromRequest(\WP_REST_Request $request)
    {
        return $this->optionGroupRepository->insertOptionGroupData($request->get_params());
    }
    public function createSingleOptionGroupItemFromRequest(\WP_REST_Request $request)
    {
        return $this->optionGroupItemsRepository->insertOptionGroupItemData($request->get_params());
    }

    public function updateSingleOptionGroupItemFromRequest(\WP_REST_Request $request)
    {
        return $this->optionGroupItemsRepository->updateOptionGroupItemData($request->get_params());
    }

    public function updateSingleOptionGroupFromRequest(\WP_REST_Request $request)
    {
        return $this->optionGroupRepository->updateOptionGroupData($request->get_params());
    }

    public function deleteOptionGroups(\WP_REST_Request $request)
    {
        $data = self::getOptionGroupRequestData($request);
        return $this->optionGroupRepository->deleteOptionGroups($data);
    }

    public function deleteOptionGroupItems(\WP_REST_Request $request)
    {
        $data = self::getOptionGroupItemsRequestData($request);
        if (
            !isset($data) ||
            !is_array($data)
        ) {
            return false;
        }
        return $this->optionGroupItemsRepository->deleteOptionGroupItems($data);
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
     * @return Tru_Fetcher_DB_Repository_Option_Groups
     */
    public function getOptionGroupRepository(): Tru_Fetcher_DB_Repository_Option_Groups
    {
        return $this->optionGroupRepository;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Option_Group_Items
     */
    public function getOptionGroupItemsRepository(): Tru_Fetcher_DB_Repository_Option_Group_Items
    {
        return $this->optionGroupItemsRepository;
    }
}
