<?php

namespace TruFetcher\Includes\Helpers;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Keymap;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Tab_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Keymap;
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
class Tru_Fetcher_Api_Helpers_Keymaps {

    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;
    public const ERROR_PREFIX = TRU_FETCHER_ERROR_PREFIX . '_tab_preset';

    private Tru_Fetcher_DB_Engine $db;
    protected Tru_Fetcher_DB_Model_Keymap $keymapModel;

    private Tru_Fetcher_DB_Repository_Keymap $keymapRepository;

    public function __construct()
    {
        $this->keymapModel = new Tru_Fetcher_DB_Model_Keymap();
        $this->keymapRepository = new Tru_Fetcher_DB_Repository_Keymap();
        $this->db = new Tru_Fetcher_DB_Engine();
    }

    public function buildKeymapData(?array $keymapData = [])
    {
        $rc = new \ReflectionClass(\WP_Post::class);
        $data = [];
        foreach ($rc->getProperties() as $property) {
            $item = [
                'key' => $property->getName()
            ];
            $findIndex = array_search($property->getName(), array_column($keymapData, 'key'));
            if ($findIndex !== false) {
                $item['keymap'] = $keymapData[$findIndex]['keymap'];
            } else {
                $item['keymap'] = '';
            }
            $data[] = $item;
        }
        return $data;
    }
    public function getKeymap(int $serviceId) {
        $keymap = $this->keymapRepository->findKeymapByServiceId($serviceId);
        if (!$keymap) {
            return $this->buildKeymapData();
        }

        return $this->buildKeymapData(
            $keymap[$this->keymapModel->getKeymapColumn()]
        );
    }

    public function saveKeymapFromRequest(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->keymapModel->getServiceIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        if (
            empty($requestData[$this->keymapModel->getKeymapColumn()]) ||
            !is_array($requestData[$this->keymapModel->getKeymapColumn()])
        ) {
            $this->addError(new \WP_Error('missing_keymap_data', 'Missing keymap data'));
            return false;
        }
        if (!$this->keymapRepository->saveKeymap(
            $requestData[$this->keymapModel->getServiceIdColumn()],
            $requestData
        )) {
            $this->errors = array_merge($this->errors, $this->keymapRepository->getErrors());
            return false;
        }
        return true;
    }

    public function deleteKeymap(\WP_REST_Request $request)
    {
        $requestData = $request->get_params();
        if (empty($requestData[$this->keymapModel->getIdColumn()])) {
            $this->addError(new \WP_Error('missing_id', 'Missing id'));
            return false;
        }
        return $this->keymapRepository->deleteById($requestData[$this->keymapModel->getIdColumn()]);
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
    public function getKeymapModel(): Tru_Fetcher_DB_Model_Tab_Presets
    {
        return $this->keymapModel;
    }

    /**
     * @return Tru_Fetcher_DB_Repository_Tab_Presets
     */
    public function getKeymapRepository(): Tru_Fetcher_DB_Repository_Tab_Presets
    {
        return $this->keymapRepository;
    }


}
