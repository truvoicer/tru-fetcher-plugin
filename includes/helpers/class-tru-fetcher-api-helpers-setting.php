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
class Tru_Fetcher_Api_Helpers_Setting
{

    use Tru_Fetcher_DB_Traits_WP_Site;

    public const ERROR_PREFIX = 'tru_fetcher_error_settings';

    private Tru_Fetcher_DB_Engine $db;
    protected Tru_Fetcher_DB_Model_Settings $settingsModel;

    private Tru_Fetcher_DB_Repository_Settings $settingsRepository;

    public function __construct()
    {
        $this->settingsModel = new Tru_Fetcher_DB_Model_Settings();
        $this->settingsRepository = new Tru_Fetcher_DB_Repository_Settings();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function getWordpressSettings()
    {
        return [
            "admin_email" => get_option("admin_email"),
            "blogname" => get_option("blogname"),
            "blogdescription" => get_option("blogdescription"),
            "blog_charset" => get_option("blog_charset"),
            "date_format" => get_option("date_format"),
            "default_category" => get_option("default_category"),
            "home" => get_option("home"),
            "siteurl" => get_option("siteurl"),
            "posts_per_page" => get_option("posts_per_page"),
        ];
    }

    public function getFormattedSettings(?array $exclude = [])
    {
        $formattedSettings = [];
        foreach ($this->getSettings($exclude) as $setting) {
            $formattedSettings[$setting[$this->settingsModel->getNameColumn()]] = $setting[$this->settingsModel->getValueColumn()];
        }
        return $formattedSettings;
    }

    public function getSettingsByNames(?array $names = [])
    {
        foreach ($names as $key) {
            $this->settingsRepository->addWhere(
                $this->settingsModel->getNameColumn(), 
                $key,
                '=',
                'OR'
            );
        }
        return $this->settingsRepository->findMany();
    }

    public function getSettings(?array $exclude = [])
    {
        foreach ($exclude as $key) {
            $this->settingsRepository->addWhere($this->settingsModel->getNameColumn(), $key, '!=');
        }
        return $this->settingsRepository->findMany();
    }

    public function getSetting(string $name)
    {
        $setting = $this->settingsRepository->findSettingByName($name);
        if (!$setting) {
            return false;
        }
        if (!empty($setting[$this->settingsModel->getValueColumn()])) {
            return $setting[$this->settingsModel->getValueColumn()];
        }
        return false;
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
