<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Form_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Form_Presets;
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
class Tru_Fetcher_Api_Helpers_Form_Presets {

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_form_preset';

    private Tru_Fetcher_DB_Engine $db;
    protected Tru_Fetcher_DB_Model_Form_Presets $formPresetsModel;

    private Tru_Fetcher_DB_Repository_Form_Presets $formPresetsRepository;

    public function __construct()
    {
        $this->formPresetsModel = new Tru_Fetcher_DB_Model_Form_Presets();
        $this->formPresetsRepository = new Tru_Fetcher_DB_Repository_Form_Presets();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function getFormPreset(string $name) {
        $formPreset = $this->formPresetsRepository->findFormPresetByName($name);
        if (!$formPreset) {
            return false;
        }

        return $formPreset;
    }

    public function createFormPresetFromRequest(\WP_REST_Request $request)
    {
        if (!$this->formPresetsRepository->insertFormPreset($request->get_params())) {
            $this->errors = array_merge($this->errors, $this->formPresetsRepository->getErrors());
            return false;
        }
        return true;
    }

    public function updateFormPresetFromRequest(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->formPresetsModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        if (!$this->formPresetsRepository->updateFormPreset(
            $requestData[$this->formPresetsModel->getIdColumn()],
            $request->get_params()
        )) {
            $this->errors = array_merge($this->errors, $this->formPresetsRepository->getErrors());
            return false;
        }
        return true;
    }

    public function deleteFormPreset(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->formPresetsModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        return $this->formPresetsRepository->deleteById($requestData[$this->formPresetsModel->getIdColumn()]);
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
     * @return Tru_Fetcher_DB_Model_Form_Presets
     */
    public function getFormPresetsModel(): Tru_Fetcher_DB_Model_Form_Presets
    {
        return $this->formPresetsModel;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Form_Presets
     */
    public function getFormPresetsRepository(): Tru_Fetcher_DB_Repository_Form_Presets
    {
        return $this->formPresetsRepository;
    }


}
