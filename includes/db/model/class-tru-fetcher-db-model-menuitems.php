<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_MenuItems extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tr_news_app_menuitems';
    public string $tableName = self::TABLE_NAME;
    protected bool $dateInserts = false;

    protected array $tableConfig = [];

    private string $menuIdColumn = 'menu_id';
    private string $categoryOptionsIdColumn = 'category_options_id';
    private string $nameColumn = 'name';
    private string $initialScreenColumn = 'initial_screen';
    private string $activeColumn = 'active';
    private string $typeColumn = 'type';
    private string $screenColumn = 'screen';
    private string $iconColumn = 'icon';
    private string $postIdColumn = 'post_id';
    private string $articlesShowAllColumn = 'articles_show_all';
    private string $articlesSortByColumn = 'articles_sort_by';
    private string $articlesSortOrderColumn = 'articles_sort_order';
    private string $featuredArticlesShowColumn = 'featured_articles_show';
    private string $featuredArticlesShowMultipleColumn = 'featured_articles_show_multiple';
    private string $featuredArticlesMultipleModeColumn = 'featured_articles_multiple_mode';
    private string $featuredArticlesSlideshowTimerColumn = 'featured_articles_slideshow_timer';
    private string $categoryOptionsOverrideColumn = 'category_options_override';

    public function __construct()
    {
        parent::__construct();
        $menuModel = new Tru_Fetcher_DB_Model_Menu();
        $categoryOptionsModel = new Tru_Fetcher_DB_Model_Category_Options();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getMenuIdColumn() => "mediumint(9) NOT NULL",
                $this->getCategoryOptionsIdColumn() => 'mediumint(9) NULL DEFAULT NULL',
                $this->getNameColumn() => 'varchar(250) NOT NULL',
                $this->getInitialScreenColumn() => 'tinyint(1) NOT NULL DEFAULT 0',
                $this->getActiveColumn() => 'tinyint(1) NOT NULL DEFAULT 0',
                $this->getTypeColumn() => 'varchar(100) NOT NULL DEFAULT "screen"',
                $this->getScreenColumn() => 'varchar(100) NOT NULL',
                $this->getIconColumn() => 'varchar(100) NOT NULL DEFAULT "fa-code"',
                $this->getPostIdColumn() => 'bigint(20) NULL DEFAULT NULL',
                $this->getArticlesShowAllColumn() => 'tinyint(1) NOT NULL DEFAULT 0',
                $this->getArticlesSortByColumn() => 'varchar(100) NOT NULL DEFAULT "date_created"',
                $this->getArticlesSortOrderColumn() => 'varchar(100) NOT NULL DEFAULT "descending"',
                $this->getFeaturedArticlesShowColumn() => 'tinyint(1) NOT NULL DEFAULT 0',
                $this->getFeaturedArticlesShowMultipleColumn() => 'tinyint(1) NOT NULL DEFAULT 0',
                $this->getFeaturedArticlesMultipleModeColumn() => 'varchar(100) NOT NULL DEFAULT "slideshow"',
                $this->getFeaturedArticlesSlideshowTimerColumn() => 'int(11) NOT NULL DEFAULT 3600',
                $this->getCategoryOptionsOverrideColumn() => 'tinyint(1) NOT NULL DEFAULT 0',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'menuItems',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
            Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD => [
                [
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN => $this->getMenuIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE => [
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL => $menuModel,
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN => $menuModel->getIdColumn()
                    ]
                ],
                [
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_CASCADE_DELETE => false,
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN => $this->getCategoryOptionsIdColumn(),
                    Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE => [
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL => $categoryOptionsModel,
                        Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN => $categoryOptionsModel->getIdColumn()
                    ]
                ],
            ]
        ];
    }


    /**
     * @return string
     */
    public function getCategoryOptionsIdColumn(): string
    {
        return $this->categoryOptionsIdColumn;
    }

    /**
     * @return string
     */
    public function getNameColumn(): string
    {
        return $this->nameColumn;
    }

    /**
     * @return string
     */
    public function getInitialScreenColumn(): string
    {
        return $this->initialScreenColumn;
    }

    /**
     * @return string
     */
    public function getActiveColumn(): string
    {
        return $this->activeColumn;
    }

    /**
     * @return string
     */
    public function getTypeColumn(): string
    {
        return $this->typeColumn;
    }

    /**
     * @return string
     */
    public function getScreenColumn(): string
    {
        return $this->screenColumn;
    }

    /**
     * @return string
     */
    public function getIconColumn(): string
    {
        return $this->iconColumn;
    }

    /**
     * @return string
     */
    public function getPostIdColumn(): string
    {
        return $this->postIdColumn;
    }

    /**
     * @return string
     */
    public function getArticlesShowAllColumn(): string
    {
        return $this->articlesShowAllColumn;
    }

    /**
     * @return string
     */
    public function getArticlesSortByColumn(): string
    {
        return $this->articlesSortByColumn;
    }

    /**
     * @return string
     */
    public function getArticlesSortOrderColumn(): string
    {
        return $this->articlesSortOrderColumn;
    }

    /**
     * @return string
     */
    public function getFeaturedArticlesShowColumn(): string
    {
        return $this->featuredArticlesShowColumn;
    }

    /**
     * @return string
     */
    public function getFeaturedArticlesShowMultipleColumn(): string
    {
        return $this->featuredArticlesShowMultipleColumn;
    }

    /**
     * @return string
     */
    public function getFeaturedArticlesMultipleModeColumn(): string
    {
        return $this->featuredArticlesMultipleModeColumn;
    }

    /**
     * @return string
     */
    public function getFeaturedArticlesSlideshowTimerColumn(): string
    {
        return $this->featuredArticlesSlideshowTimerColumn;
    }

    /**
     * @return string
     */
    public function getCategoryOptionsOverrideColumn(): string
    {
        return $this->categoryOptionsOverrideColumn;
    }

    /**
     * @return string
     */
    public function getMenuIdColumn(): string
    {
        return $this->menuIdColumn;
    }

}
