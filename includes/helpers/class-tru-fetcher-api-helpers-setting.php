<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Settings;
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
class Tru_Fetcher_Api_Helpers_Setting {

    use Tru_Fetcher_DB_Traits_WP_Site;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_settings';

    protected Tru_Fetcher_DB_Model_Settings $settingsModel;

    private Tru_Fetcher_DB_Repository_Settings $settingsRepository;

    public function __construct()
    {
        $this->settingsModel = new Tru_Fetcher_DB_Model_Settings();
        $this->settingsRepository = new Tru_Fetcher_DB_Repository_Settings();
    }

    public static function getSettingsRequestData(\WP_REST_Request $request)
    {
        return $request->get_param('settings');
    }


    public function createSettingsBatchFromRequest(\WP_REST_Request $request)
    {
        $this->settingsRepository->createSettingsBatch($request->get_params());
        return true;
    }

    public function createSingleSettingsFromRequest(\WP_REST_Request $request)
    {
        return $this->settingsRepository->insertSettingsData($request->get_params());
    }

    public function updateSingleSettingsFromRequest(\WP_REST_Request $request)
    {
        return $this->settingsRepository->updateSettingsData($request->get_params());
    }

    public function deleteSettings(\WP_REST_Request $request)
    {
        $data = self::getSettingsRequestData($request);
        if (
            !isset($data) ||
            !is_array($data)
        ) {
            return false;
        }
        return $this->settingsRepository->deleteSettings($data);
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
     * @return Tru_Fetcher_DB_Repository_Settings
     */
    public function getSettingsRepository(): Tru_Fetcher_DB_Repository_Settings
    {
        return $this->settingsRepository;
    }

}
