<?php
namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Topic;

class Tru_Fetcher_DB_Data_Topic extends Tru_Fetcher_DB_Data
{
    private Tru_Fetcher_DB_Repository_Topic $topicRepository;
    private array $data = [];
    public function __construct()
    {
        $this->setModel(new Tru_Fetcher_DB_Model_Topic());
        $this->topicRepository = new Tru_Fetcher_DB_Repository_Topic();
        $nowDate = new \DateTime();
        $this->data = [
            [
                $this->model->getTopicNameColumn() => Tru_Fetcher_DB_Model_Topic::DEFAULT_TOPIC,
                $this->model->getDateUpdatedColumn() => $nowDate,
                $this->model->getDateCreatedColumn() => $nowDate,
            ],
        ];
    }


    public function install() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->topicRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $topic) {
            $insertTopic = $this->topicRepository->insertTopic(
                $topic[$this->model->getTopicNameColumn()]
            );
            if (!$insertTopic) {
                $this->errors[] = "Error inserting topic at position {$index}";
            }
        }
        if (count($this->errors)) {
            return [
                'success' => false,
                'errors' => $this->errors
            ];
        }
        return [
            'success' => true,
        ];
    }

    public function check() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->topicRepository->setSite($this->site);
        }
        foreach ($this->data as $index => $topic) {
            $findTopic = $this->topicRepository->fetchTopicByName($topic[$this->model->getTopicNameColumn()]);
            if (!$findTopic) {
                $this->errors[] = "Error finding topic at position {$index}";
                return [
                    'success' => false,
                    'errors' => $this->errors
                ];
            }
        }
        return [
            'success' => true,
        ];
    }
}
