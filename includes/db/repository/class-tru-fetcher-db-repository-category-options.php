<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Category;
use TruFetcher\Includes\Helpers\Tru_Fetcher_Api_Helpers_Menu;
use TruFetcher\Includes\Api\Response\Admin\Tru_Fetcher_Api_Admin_Category_Options_Response;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Category_Options;
use TruFetcher\Includes\DB\Model\WP\Tru_Fetcher_DB_Model_WP_Term;
use TruFetcher\Includes\Taxonomies\Tru_Fetcher_Taxonomy;

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
class Tru_Fetcher_DB_Repository_Category_Options extends Tru_Fetcher_DB_Repository_Base
{
    public const EXISTING_CATEGORY_ERROR_CODE = '_exists';
    private Tru_Fetcher_DB_Model_WP_Term $wpTermModel;
    private Tru_Fetcher_Taxonomy $taxonomy;

    private Tru_Fetcher_DB_Model_Category_Options $categoryOptionsModel;

    public function __construct()
    {
        $this->categoryOptionsModel = new Tru_Fetcher_DB_Model_Category_Options();
        parent::__construct($this->categoryOptionsModel);
        $this->taxonomy = new Tru_Fetcher_Taxonomy();
        $this->wpTermModel = new Tru_Fetcher_DB_Model_WP_Term();
    }

    public function findCategoryOptions(?bool $includeWithParents = false)
    {
        $menuItemsRepository = new Tru_Fetcher_DB_Repository_Menu_Items();
        $menu = new Tru_Fetcher_Api_Helpers_Menu();
        $results = $this->db->getAllResults(
            new Tru_Fetcher_DB_Model_Category_Options(),
            ARRAY_A
        );

        $menus = $menuItemsRepository->findAllMenuItems();
        if (!is_array($menus)) {
            return $this->model->buildModelDataBatch($results);
        }
        if (!$includeWithParents) {
            $menuItems = array_map(function ($menuItem) {
                return $menuItem['category_options_id'];
            }, $menus);

            $results = array_filter($results, function ($item) use($menuItems) {
                return !in_array($item['id'], $menuItems);
            }, ARRAY_FILTER_USE_BOTH);
            $results = array_values($results);
        }
        return $this->model->buildModelDataBatch($results);
    }

    public function findCategoryOptionsById($categoryId)
    {

        $findExistingCategoryOption = $this->db->getSingleResult(
            new Tru_Fetcher_DB_Model_Category_Options(),
            "id=%d",
            [$categoryId],
            ARRAY_A
        );

        if (is_array($findExistingCategoryOption) && !empty($findExistingCategoryOption)) {
            return $this->model->buildModelData($findExistingCategoryOption);
        }
        return false;
    }

    private function findTermForCategoryOptionsBatch($data) {
        return array_map(function ($item) {
            return $this->findTermForCategoryOption($item);
        }, $data);
    }

    private function findTermForCategoryOption($result) {
        if (!$result) {
            return false;
        }
        $fk = Tru_Fetcher_DB_Model::findForeignKeyByReferenceModel($this->wpTermModel,$this->model->getForeignKeys());
        $parentResultsData = $this->wpTermModel->getData($fk, $result);
        if (!$parentResultsData) {
            return $result;
        }
        $result['term'] = $parentResultsData;
        return $result;
    }

    public function findCategoryOption(int $id)
    {
        $result = $this->db->getSingleResult(
            $this->model,
            "{$this->model->getIdColumn()}=%d",
            [$id],
            ARRAY_A
        );
        return $this->findTermForCategoryOption($result);
    }


    public function findCategoryOptionsByTaxonomyTerm(\WP_Term $term)
    {
        $results = $this->db->getResults(
            $this->model,
            "{$this->model->getTermIdColumn()}=%d AND {$this->model->getTaxonomyColumn()}=%d",
            [$term->term_id, $term->taxonomy]
        );
        if (!$results) {
            return false;
        }
        return $results;
    }

    public function doesCategoryOptionExistForTerm($term) {
        $findExistingTermCategoryOption = $this->findCategoryOptionsByTaxonomyTerm($term);
        if ($findExistingTermCategoryOption) {
            $this->addError(
                new \WP_Error(
                    Tru_Fetcher_Api_Admin_Category_Options_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_term_error',
                    sprintf(
                        'Category option for term: %s already exists',
                        $term->name
                    )
                )
            );
            return false;
        }
        return true;
    }

    protected function clearCategoryOptionsTables()
    {
        global $wpdb;
        $wpdb->query($wpdb->prepare("DELETE FROM {$this->model->getTableName()};"));
    }

    public function buildCategoryOptionsTermsData(string $dbOperation, array $data, $termKey, ?bool $isMenuItemChild = false)
    {
        $errorsEntity = $this->getDb()->getErrors();
        if (is_wp_error($termKey)) {
            return $data;
        }
        $getTerm = Tru_Fetcher_Api_Helpers_Category::getTermFromCategoryOptionsData($termKey, $data);
        if (is_wp_error($getTerm)) {
            $errorsEntity->addToFetchErrors($getTerm);
            return false;
        } elseif (!$getTerm) {
            $errorsEntity->addToFetchErrors(
                new \WP_Error(
                    Tru_Fetcher_Api_Admin_Category_Options_Response::API_RESPONSE_ERROR_CODE_PREFIX . '_term_error',
                    'Error retrieving term'
                )
            );
            return false;
        }
        if (!$isMenuItemChild) {
            switch ($dbOperation) {
                case $this->db::DB_OPERATION_INSERT:
                    if (!$this->doesCategoryOptionExistForTerm($getTerm)) {
                        return false;
                    }
                    break;
            }
        }
        $data[$this->model->getTermIdColumn()] = $getTerm->term_id;
        $data[$this->model->getTaxonomyColumn()] = $getTerm->taxonomy;
        return $data;
    }

    public function insertCategoryOptions(string $dbOperation, array $data, $termKey, ?bool $isMenuItemChild = false)
    {
        if (is_wp_error($termKey)) {
            $this->db->getErrors()->addToInsertErrors($termKey);
            return false;
        }
        $categoryOptionsData = $this->buildCategoryOptionsTermsData($dbOperation, $data, $termKey, $isMenuItemChild);
        if (!$categoryOptionsData) {
            return false;
        }
        $insert = $this->insert($categoryOptionsData);
        if (!$insert) {
            return false;
        }
        return $insert;
    }

    private function updateCategoryOptions(string $dbOperation, array $data, $termKey, ?bool $isMenuItemChild = false)
    {
        $categoryOptionsData = $this->buildCategoryOptionsTermsData($dbOperation, $data, $termKey, $isMenuItemChild);
        if (!$categoryOptionsData) {
            return false;
        }
        return $this->update($categoryOptionsData);
    }

    private function saveBatchCategoryOptions(string $dbOperation, array $data)
    {
        foreach ($data as $categoryOption) {
            $termKey = Tru_Fetcher_Api_Helpers_Category::getTermKeyFromCategoryOptionsData($categoryOption);
            switch ($dbOperation) {
                case $this->db::DB_OPERATION_INSERT:
                    $this->insertCategoryOptions($dbOperation, $categoryOption, $termKey);
                    break;
                case $this->db::DB_OPERATION_UPDATE:
                    $this->updateCategoryOptions($dbOperation, $categoryOption, $termKey);
                    break;
            }
        }
    }

    public function createNewsAppCategoryOptions(array $data, ?bool $isMenuItemChild = false)
    {
        $termKey = Tru_Fetcher_Api_Helpers_Category::getTermKeyFromCategoryOptionsData($data);
        return $this->insertCategoryOptions($this->db::DB_OPERATION_INSERT, $data, $termKey, $isMenuItemChild);
    }

    public function updateNewsAppCategoryOptions(array $data, ?bool $isMenuItemChild = false)
    {
        $this->setWhereQueryConditions([
            [
                Tru_Fetcher_DB_Model_Constants::FIELD_KEY => $this->model->getPrimaryKey(),
                Tru_Fetcher_DB_Model_Constants::DATA_TYPE_KEY =>Tru_Fetcher_DB_Model_Constants::DATA_TYPE_INT,
                Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_KEY => Tru_Fetcher_DB_Model_Constants::DEFAULT_WHERE_COMPARE,
            ],
        ]);
        $termKey = Tru_Fetcher_Api_Helpers_Category::getTermKeyFromCategoryOptionsData($data);
        return $this->updateCategoryOptions($this->db::DB_OPERATION_UPDATE, $data, $termKey, $isMenuItemChild);
    }

    public function deleteNewsAppCategoryOptions(array $data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

    /**
     * @return Tru_Fetcher_DB_Model_Category_Options
     */
    public function getCategoryOptionsModel(): Tru_Fetcher_DB_Model_Category_Options
    {
        return $this->categoryOptionsModel;
    }

}
