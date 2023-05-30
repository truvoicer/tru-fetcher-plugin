<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device_Topic;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;

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
class Tru_Fetcher_DB_Repository_Device_Topic extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Device_Topic());
    }

    public function buildInsertData(int $deviceId, int $topicId) {
        $data = [
            $this->model->getDeviceIdColumn() => $deviceId,
            $this->model->getTopicIdColumn() => $topicId,
        ];
        return $data;
    }
    public function buildUpdateData(int $id, array $data) {
        $data[$this->model->getIdColumn()] = $id;
        return $data;
    }
    public function insertDeviceTopic(int $deviceId, int $topicId)
    {
        $buildInsertData = $this->buildInsertData($deviceId, $topicId);
        if (!$buildInsertData) {
            return false;
        }
        return $this->insert($buildInsertData);
    }

    public function updateDeviceTopic(int $id, array $data)
    {
        $buildUpdateData = $this->buildUpdateData($id, $data);
        if (!$buildUpdateData) {
            return false;
        }
        return $this->update($buildUpdateData);
    }

    public function deleteDeviceTopic(array $data)
    {
        return $this->deleteMany($data);
    }

}
