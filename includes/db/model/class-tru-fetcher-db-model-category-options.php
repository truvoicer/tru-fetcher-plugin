<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\WP\Tru_Fetcher_DB_Model_WP_Term;

class Tru_Fetcher_DB_Model_Category_Options extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_category_options';
    public string $tableName = self::TABLE_NAME;
    protected bool $dateInserts = false;

    protected array $tableConfig = [];

    private string $termIdColumn = 'term_id';
    private string $taxonomyColumn = 'taxonomy';
    private string $categoryIconColumn = 'category_icon';
    private string $categoryCardIconColorColumn = 'category_card_icon_color';
    private string $categoryCardBgColorColumn = 'category_card_bg_color';
    private string $categoryCardTextColorColumn = 'category_card_text_color';

    public function __construct()
    {
        parent::__construct();
        $wpTermModel = new Tru_Fetcher_DB_Model_WP_Term();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getTermIdColumn() => 'bigint(20) NULL',
                $this->getTaxonomyColumn() => 'varchar(512) NOT NULL',
                $this->getCategoryIconColumn() => 'varchar(100) NOT NULL DEFAULT "fa-code"',
                $this->getCategoryCardIconColorColumn() => 'varchar(100) NOT NULL DEFAULT "#cccccc"',
                $this->getCategoryCardBgColorColumn() => 'varchar(100) NOT NULL DEFAULT "rgba(201,44,33,0.7)"',
                $this->getCategoryCardTextColorColumn() => 'varchar(100) NOT NULL DEFAULT "#cccccc"',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'categoryOptions',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
            Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD => [
                [
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_WP_REFERENCE => true,
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN => [$this->getTermIdColumn(), $this->getTaxonomyColumn()],
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE => [
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL => $wpTermModel,
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN => [$wpTermModel->getTermIdField(), $wpTermModel->getTaxonomyField()]
                    ]
                ]
            ]
        ];
    }


    /**
    /**
     * @return string
     */
    public function getTermIdColumn(): string
    {
        return $this->termIdColumn;
    }

    /**
     * @return string
     */
    public function getTaxonomyColumn(): string
    {
        return $this->taxonomyColumn;
    }
    /**
     * @return string
     */
    public function getCategoryIconColumn(): string
    {
        return $this->categoryIconColumn;
    }

    /**
     * @return string
     */
    public function getCategoryCardIconColorColumn(): string
    {
        return $this->categoryCardIconColorColumn;
    }

    /**
     * @return string
     */
    public function getCategoryCardBgColorColumn(): string
    {
        return $this->categoryCardBgColorColumn;
    }

    /**
     * @return string
     */
    public function getCategoryCardTextColorColumn(): string
    {
        return $this->categoryCardTextColorColumn;
    }


}
