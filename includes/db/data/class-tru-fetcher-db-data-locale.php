<?php
namespace TruFetcher\Includes\DB\data;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Locale;
use TruFetcher\Includes\DB\Repository\Tru_Fetcher_DB_Repository_Locale;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

class Tru_Fetcher_DB_Data_Locale extends Tru_Fetcher_DB_Data
{
    private Tru_Fetcher_DB_Repository_Locale $localeRepository;
    private Tru_Fetcher_DB_Model_Locale $localeModel;
    public function __construct()
    {
        $this->localeModel = new Tru_Fetcher_DB_Model_Locale();
        $this->setModel($this->localeModel);
        $this->localeRepository = new Tru_Fetcher_DB_Repository_Locale();
    }

    public function install() {
        if (!$this->doesTableExist()) {
            return [
                'success' => false,
            ];
        }
        if ($this->site instanceof \WP_Site) {
            $this->localeRepository->setSite($this->site);
        }
        foreach ($this->buildInitialData() as $index => $localeData) {
            $insertLocale = $this->localeRepository->insertLocale($localeData);
            if (!$insertLocale) {
                $this->errors[] = "Error inserting form preset ({$localeData[$this->localeModel->getCountryNameColumn()]}) at position {$index}";
            }
        }
        if ($this->localeRepository->hasErrors()) {
           $this->errors = array_merge($this->errors, $this->localeRepository->getErrors());
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
            $this->localeRepository->setSite($this->site);
        }
        foreach ($this->buildInitialData() as $index => $locale) {
            $findLocale = $this->localeRepository->findLocaleByCountrySlug($locale[$this->localeModel->getCountrySlugColumn()]);
            if (!$findLocale) {
                $this->errors[] = sprintf(
                    "Error finding locale | country_slug (%s) | position %d",
                    $locale[$this->localeModel->getCountrySlugColumn()],
                    $index
                );
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

    private function buildInitialData() {

        $iso3Codes = file_get_contents(TRU_FETCHER_PLUGIN_DIR . 'config/locale/iso3.json');
        $decodeIso3 = json_decode($iso3Codes, true);
        $names = file_get_contents(TRU_FETCHER_PLUGIN_DIR . 'config/locale/names.json');
        $decodeNames = json_decode($names, true);
        $phone = file_get_contents(TRU_FETCHER_PLUGIN_DIR . 'config/locale/phone.json');
        $decodePhone = json_decode($phone, true);
        $currencyCodes = file_get_contents(TRU_FETCHER_PLUGIN_DIR . 'config/locale/currency.json');
        $decodeCurrencyCodes = json_decode($currencyCodes, true);
        $currencyInfoCodes = file_get_contents(TRU_FETCHER_PLUGIN_DIR . 'config/locale/currency-codes.json');
        $decodeCurrencyInfoCodes = json_decode($currencyInfoCodes, true);
        $countries = [];
        foreach ($decodeNames as $iso2Code => $countryName) {
            $iso3Code = $decodeIso3[$iso2Code];
            $phoneCode = $decodePhone[$iso2Code];
            $currencyCode = $decodeCurrencyCodes[$iso2Code];
            $countrySlug = Tru_Fetcher_Helpers::toSnakeCase($countryName);
            $countryData = [
                'country_name' => $countryName,
                'country_slug' => $countrySlug,
                'country_iso2' => $iso2Code,
                'country_iso3' => $iso3Code,
                'country_phone_code' => $phoneCode
            ];
            if (array_key_exists($currencyCode, $decodeCurrencyInfoCodes)) {
                $currencyInfoCode = $decodeCurrencyInfoCodes[$currencyCode];
                $countryData = array_merge(
                    $countryData,
                    [
                        'currency_name' => $currencyInfoCode['name'],
                        'currency_name_plural' => $currencyInfoCode['name_plural'],
                        'currency_code' => $currencyInfoCode['code'],
                        'currency_symbol' => $currencyInfoCode['symbol']
                    ]
                );
            }
            $countries[] = $countryData;
        }
        return $countries;
    }
}
