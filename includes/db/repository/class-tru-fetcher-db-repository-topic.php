<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;
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
class Tru_Fetcher_DB_Repository_Topic extends Tru_Fetcher_DB_Repository_Base {

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Topic());
    }

    public function getTopicNames(array $topics) {
        return array_map(function ($topic) {
            return $topic[$this->model->getTopicNameColumn()];
        }, $topics);
    }
    public function fetchTopicByName(string $topicName)
    {
        $this->addWhere($this->model->getTopicNameColumn(), $topicName);
        return $this->findOne();
    }

    public function buildInsertData(string $topicName) {
        $data = [
            $this->model->getTopicNameColumn() => $topicName,
        ];
        return $data;
    }
    public function buildUpdateData(int $id, array $data) {
        $data[$this->model->getIdColumn()] = $id;
        return $data;
    }
    public function insertTopic(string $topicName)
    {
        $buildInsertData = $this->buildInsertData($topicName);
        if (!$buildInsertData) {
            return false;
        }
        return $this->insert($buildInsertData);
    }

    public function updateTopic(int $id, array $data)
    {
        $buildUpdateData = $this->buildUpdateData($id, $data);
        if (!$buildUpdateData) {
            return false;
        }
        return $this->update($buildUpdateData);
    }

    public function deleteTopic(array $data)
    {
        return $this->deleteMany($data);
    }

}
