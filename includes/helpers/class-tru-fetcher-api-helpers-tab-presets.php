<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Tab_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Tab_Presets;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Settings;
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
class Tru_Fetcher_Api_Helpers_Tab_Presets {

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_tab_preset';

    private Tru_Fetcher_DB_Engine $db;
    protected Tru_Fetcher_DB_Model_Tab_Presets $tabPresetsModel;

    private Tru_Fetcher_DB_Repository_Tab_Presets $tabPresetsRepository;

    public function __construct()
    {
        $this->tabPresetsModel = new Tru_Fetcher_DB_Model_Tab_Presets();
        $this->tabPresetsRepository = new Tru_Fetcher_DB_Repository_Tab_Presets();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function getTabPreset(string $name) {
        $tabPreset = $this->tabPresetsRepository->findTabPresetByName($name);
        if (!$tabPreset) {
            return false;
        }

        return $tabPreset;
    }

    public function createTabPresetFromRequest(\WP_REST_Request $request)
    {
        if (!$this->tabPresetsRepository->insertTabPreset($request->get_params())) {
            $this->errors = array_merge($this->errors, $this->tabPresetsRepository->getErrors());
            return false;
        }
        return true;
    }

    public function updateTabPresetFromRequest(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->tabPresetsModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        if (!$this->tabPresetsRepository->updateTabPreset(
            $requestData[$this->tabPresetsModel->getIdColumn()],
            $request->get_params()
        )) {
            $this->errors = array_merge($this->errors, $this->tabPresetsRepository->getErrors());
            return false;
        }
        return true;
    }

    public function deleteTabPreset(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->tabPresetsModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        return $this->tabPresetsRepository->deleteById($requestData[$this->tabPresetsModel->getIdColumn()]);
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
     * @return Tru_Fetcher_DB_Model_Tab_Presets
     */
    public function getTabPresetsModel(): Tru_Fetcher_DB_Model_Tab_Presets
    {
        return $this->tabPresetsModel;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Tab_Presets
     */
    public function getTabPresetsRepository(): Tru_Fetcher_DB_Repository_Tab_Presets
    {
        return $this->tabPresetsRepository;
    }


}
