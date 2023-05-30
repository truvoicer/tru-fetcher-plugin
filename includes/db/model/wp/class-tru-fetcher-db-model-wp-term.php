<?php

namespace TruFetcher\Includes\DB\Model\WP;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_WP_Term extends Tru_Fetcher_DB_Model_WP
{
    private string $termIdField = 'term_id';
    private string $taxonomyField = 'taxonomy';
    private string $slugField = 'slug';
    private string $nameField = 'name';
    private string $termTaxonomyIdField = 'term_taxonomy_id';

    public function __construct()
    {
        $this->setConfig([
            parent::FIELDS => [
                $this->getTermIdField(),
                $this->getTaxonomyField(),
                $this->getSlugField(),
                $this->getNameField(),
                $this->getTermTaxonomyIdField()
            ],
			Tru_Fetcher_DB_Model_Constants::ALIAS => 'term',
            self::REQUIRED_FIELDS => [$this->getTaxonomyField()],
			parent::WP_RETURN_DATA_TYPE => \WP_Term::class,
            self::MUST_HAVE_EITHER_FIELDS => [
                $this->getTermIdField(),
                $this->getTaxonomyField(),
                $this->getSlugField(),
                $this->getNameField(),
                $this->getTermTaxonomyIdField()
            ]
        ]);
    }

    public function getData(array $foreignKey, array $result)
    {
        $validateFields = $this->validateFields($foreignKey, $result);
        if (is_wp_error($validateFields)) {
            return $validateFields;
        }
        $field = $validateFields[array_key_first($validateFields)];
        $value = $result[$field];
        $term = get_term_by($field, $value, $result[$this->getTaxonomyField()]);
        if (!$term) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_term_not_found',
                'Term not found',
                $result
            );
        }
        return $term;
    }

    /**
     * @return string
     */
    public function getTermIdField(): string
    {
        return $this->termIdField;
    }

    /**
     * @param string $termIdField
     */
    public function setTermIdField(string $termIdField): void
    {
        $this->termIdField = $termIdField;
    }

    /**
     * @return string
     */
    public function getTaxonomyField(): string
    {
        return $this->taxonomyField;
    }

    /**
     * @param string $taxonomyField
     */
    public function setTaxonomyField(string $taxonomyField): void
    {
        $this->taxonomyField = $taxonomyField;
    }

    /**
     * @return string
     */
    public function getSlugField(): string
    {
        return $this->slugField;
    }

    /**
     * @param string $slugField
     */
    public function setSlugField(string $slugField): void
    {
        $this->slugField = $slugField;
    }

    /**
     * @return string
     */
    public function getNameField(): string
    {
        return $this->nameField;
    }

    /**
     * @param string $nameField
     */
    public function setNameField(string $nameField): void
    {
        $this->nameField = $nameField;
    }

    /**
     * @return string
     */
    public function getTermTaxonomyIdField(): string
    {
        return $this->termTaxonomyIdField;
    }

    /**
     * @param string $termTaxonomyIdField
     */
    public function setTermTaxonomyIdField(string $termTaxonomyIdField): void
    {
        $this->termTaxonomyIdField = $termTaxonomyIdField;
    }

}
