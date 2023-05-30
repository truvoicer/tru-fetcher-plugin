<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;

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
class Tru_Fetcher_DB_Repository_Device extends Tru_Fetcher_DB_Repository_Base {

    private Tru_Fetcher_DB_Repository_Device_Topic $deviceTopicRepository;
    private Tru_Fetcher_DB_Model_Device $deviceModel;
    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Device());
        $this->deviceTopicRepository = new Tru_Fetcher_DB_Repository_Device_Topic();
        $this->deviceModel = new Tru_Fetcher_DB_Model_Device();
    }

    public function getDeviceRegisterTokens(array $devices) {
        return array_map(function ($device) {
            return $device[$this->model->getRegisterTokenColumn()];
        }, $devices);
    }

    public function fetchUserDevice(\WP_User $user)
    {
        $this->addWhere($this->deviceModel->getUserIdColumn(), $user->ID);
        return $this->findOne();
    }

    public function fetchDeviceByRegisterToken(string $registerToken)
    {
        $this->addWhere($this->model->getRegisterTokenColumn(), $registerToken);
        return $this->findOne();
    }

    public function buildInsertData(string $token, bool $allowNotifications, ?\WP_User $user = null) {
        $data = [
            $this->model->getRegisterTokenColumn() => $token,
            $this->model->getAllowNotificationsColumn() => $allowNotifications,
        ];
        if ($user instanceof \WP_User) {
            $data[$this->model->getUserIdColumn()] = $user->ID;
        }
        return $data;
    }
    public function buildUpdateData(int $id, array $data) {
        $data[$this->model->getIdColumn()] = $id;
        return $data;
    }
    public function insertDevice(string $token, bool $allowNotifications, ?\WP_User $user = null)
    {
        $fetchDeviceByRegisterToken = $this->fetchDeviceByRegisterToken($token);
        if ($fetchDeviceByRegisterToken) {
            return $fetchDeviceByRegisterToken;
        }
        $buildInsertData = $this->buildInsertData($token, $allowNotifications, $user);
        if (!$buildInsertData) {
            return false;
        }
        return $this->insert($buildInsertData);
    }

    public function insertDeviceTopic(int $deviceId, int $topicId)
    {
        return $this->deviceTopicRepository->insertDeviceTopic($deviceId, $topicId);
    }

    public function updateDevice(int $id, array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $buildUpdateData = $this->buildUpdateData($id, $data);
        if (!$buildUpdateData) {
            return false;
        }
        return $this->update($buildUpdateData);
    }

    public function deleteDevice(array $data)
    {
        return $this->deleteMany($data);
    }

}
