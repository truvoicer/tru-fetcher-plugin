<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

class Tru_Fetcher_DB_Model_Locale extends Tru_Fetcher_DB_Model
{

    const TABLE_NAME = 'tru_fetcher_locale';
    public string $tableName = self::TABLE_NAME;
    protected bool $dateInserts = false;

    protected array $tableConfig = [];

    private string $countryNameColumn = 'country_name';
    private string $countrySlugColumn = 'country_slug';
    private string $countryIso2Column = 'country_iso2';
    private string $countryIso3Column = 'country_iso3';
    private string $countryPhoneCodeColumn = 'country_phone_code';
    private string $currencyNameColumn = 'currency_name';
    private string $currencyNamePluralColumn = 'currency_name_plural';
    private string $currencyCodeColumn = 'currency_code';
    private string $currencySymbolColumn = 'currency_symbol';


    public function __construct()
    {
        parent::__construct();
        $this->tableConfig = [
            Tru_Fetcher_DB_Model_Constants::COLUMNS => [
                $this->getIdColumn() => 'mediumint(9) NOT NULL AUTO_INCREMENT',
                $this->getCountrySlugColumn() => 'varchar(255) NOT NULL',
                $this->getCountryNameColumn() => 'varchar(255) NOT NULL',
                $this->getCountryIso2Column() => 'varchar(5) NOT NULL',
                $this->getCountryIso3Column() => 'varchar(5) NOT NULL',
                $this->getCountryPhoneCodeColumn() => 'varchar(255) NULL',
                $this->getCurrencyNameColumn() => 'varchar(255) NULL',
                $this->getCurrencyNamePluralColumn() => 'varchar(255) NULL',
                $this->getCurrencyCodeColumn() => 'varchar(5) NULL',
                $this->getCurrencySymbolColumn() => 'varchar(255) NULL',
            ],
            Tru_Fetcher_DB_Model_Constants::ALIAS => 'locale',
            Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD => $this->getIdColumn(),
        ];
        $this->setRequiredFields([
            $this->getCountrySlugColumn(),
            $this->getCountryNameColumn(),
            $this->getCountryIso2Column(),
            $this->getCountryIso3Column(),
        ]);
        $this->orderBy = [
            $this->getCountryNameColumn()
        ];
        $this->orderDir = Tru_Fetcher_DB_Model_Constants::SORT_ORDER_ASC;
    }

    /**
     * @return string
     */
    public function getCountrySlugColumn(): string
    {
        return $this->countrySlugColumn;
    }

    /**
     * @return string
     */
    public function getCountryNameColumn(): string
    {
        return $this->countryNameColumn;
    }

    /**
     * @return string
     */
    public function getCountryIso2Column(): string
    {
        return $this->countryIso2Column;
    }

    /**
     * @return string
     */
    public function getCountryIso3Column(): string
    {
        return $this->countryIso3Column;
    }

    /**
     * @return string
     */
    public function getCountryPhoneCodeColumn(): string
    {
        return $this->countryPhoneCodeColumn;
    }

    /**
     * @return string
     */
    public function getCurrencyNameColumn(): string
    {
        return $this->currencyNameColumn;
    }

    /**
     * @return string
     */
    public function getCurrencyNamePluralColumn(): string
    {
        return $this->currencyNamePluralColumn;
    }

    /**
     * @return string
     */
    public function getCurrencyCodeColumn(): string
    {
        return $this->currencyCodeColumn;
    }

    /**
     * @return string
     */
    public function getCurrencySymbolColumn(): string
    {
        return $this->currencySymbolColumn;
    }

}
