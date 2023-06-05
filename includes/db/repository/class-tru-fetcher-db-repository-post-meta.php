<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Post_Meta;

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
class Tru_Fetcher_DB_Repository_Post_Meta extends Tru_Fetcher_DB_Repository_Base
{

    protected Tru_Fetcher_DB_Model_Post_Meta $postMetaModel;

    public function __construct()
    {
        parent::__construct(new Tru_Fetcher_DB_Model_Post_Meta());
        $this->postMetaModel = new Tru_Fetcher_DB_Model_Post_Meta();
    }

    public function findByPostId(int $postId)
    {
        $this->addWhereQueryCondition(
            $this->postMetaModel->getPostIdColumn(),
            $postId
        );
        $results = $this->findMany();
        $results = $this->postMetaModel->buildModelDataBatch($results);

        return $this->optionGroupItemsRepository->findOptionGroupItemsForBatch($results);
    }

    public function findByMetaKey(int $postId, string $metaKey)
    {
        $this->addWhereQueryCondition(
            $this->postMetaModel->getPostIdColumn(),
            $postId
        );
        $this->addWhereQueryCondition(
            $this->postMetaModel->getMetaKeyColumn(),
            $metaKey
        );
        $results = $this->findMany();
        $results = $this->postMetaModel->buildModelDataBatch($results);
        return $results;
    }

    private function buildInsertData(array $data) {
        $data = [];
        $data[$this->postMetaModel->getId()] = $data['id'];
        $data[$this->postMetaModel->getPostIdColumn()] = $data['post_id'];
        $data[$this->postMetaModel->getMetaKeyColumn()] = $data['meta_key'];
        $data[$this->postMetaModel->getMetaValueColumn()] = $data['meta_value'];
        return $data;
    }
    public function insertPostMeta(array $data, ?bool $single = true)
    {
        $insertData = $this->buildInsertData($data);
        if (!$insertData) {
            return false;
        }
        if ($single && $this->findByMetaKey($insertData['post_id'], $insertData['meta_key'])) {
            $this->addError(new \WP_Error('duplicate_error', 'Post meta already exists with same key'));
            return false;
        }
        $results = $this->insert($insertData);
        return $results;
    }

    private function buildUpdateData(array $data) {
        $data = [];
        $data[$this->postMetaModel->getId()] = $data['id'];
        $data[$this->postMetaModel->getMetaValueColumn()] = $data['meta_value'];
        return $data;
    }

    public function updatePostMeta($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        $updateData = $this->buildUpdateData($data);
        if (!$updateData) {
            return false;
        }

        $results = $this->update($updateData);
        return $results;
    }

    public function deletePostMeta($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
