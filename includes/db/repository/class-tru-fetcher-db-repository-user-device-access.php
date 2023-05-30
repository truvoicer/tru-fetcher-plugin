<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_User_Device_Access;

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
class Tru_Fetcher_DB_Repository_User_Device_Access extends Tru_Fetcher_DB_Repository_Base {

    private Tru_Fetcher_DB_Repository_Device $deviceRepository;
    private Tru_Fetcher_DB_Model_User_Device_Access $deviceAccessModel;
    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_User_Device_Access());
        $this->deviceRepository = new Tru_Fetcher_DB_Repository_Device();
        $this->deviceAccessModel = new Tru_Fetcher_DB_Model_User_Device_Access();
    }

    public function fetchDeviceAccessByUser(\WP_User $user)
    {
        $this->addWhere($this->deviceAccessModel->getUserIdColumn(), $user->ID);
        return $this->findMany();
    }

    public function buildInsertData(\WP_User $user, int $deviceId) {
        $data = [
            $this->deviceAccessModel->getUserIdColumn() => $user->ID,
            $this->deviceAccessModel->getDeviceIdColumn() => $deviceId,
        ];
        return $data;
    }
    public function insertDeviceAccess(\WP_User $user, int $deviceId)
    {
        $findDevice = $this->deviceRepository->findById($deviceId);
        if (!$findDevice) {
            return false;
        }
        $buildInsertData = $this->buildInsertData($user, $deviceId);
        if (!$buildInsertData) {
            return false;
        }
        return $this->insert($buildInsertData);
    }

}
