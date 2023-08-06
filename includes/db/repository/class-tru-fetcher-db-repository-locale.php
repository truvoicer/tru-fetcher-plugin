<?php

namespace TruFetcher\Includes\DB\Repository;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Locale;

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
class Tru_Fetcher_DB_Repository_Locale extends Tru_Fetcher_DB_Repository_Base {

    private Tru_Fetcher_DB_Model_Locale $localeModel;

    public function __construct()
    {
        $this->localeModel = new Tru_Fetcher_DB_Model_Locale();
        parent::__construct($this->localeModel);
    }

    public function findLocales()
    {
       return $this->findMany();
    }

    public function findLocaleByCountrySlug(string $countrySlug)
    {
        $this->addWhere($this->localeModel->getCountrySlugColumn(), $countrySlug);
        return $this->findOne();
    }

    private function buildLocaleData(array $requestData)
    {
        foreach ($this->localeModel->getRequiredFields() as $field) {
            if (empty($requestData[$field])) {
                $this->addError(new \WP_Error('missing_field', "Missing field {$field}"));
                return false;
            }
        }
        return $requestData;
    }

    public function insertLocale($data)
    {
        $locale = $this->buildLocaleData($data);
        if (!$locale) {
            return false;
        }
        $fetch = $this->findLocaleByCountrySlug($locale[$this->localeModel->getCountrySlugColumn()]);
        if ($fetch) {
            $this->addError(new \WP_Error('duplicate_error', 'Locale already exists with same country name'));
            return false;
        }
        return $this->insert($locale);
    }

    private function buildLocaleUpdateData(int $id, array $requestData)
    {
        $data = array_filter($requestData, function ($key) {
            return (
                $key !== $this->localeModel->getIdColumn() &&
                in_array($key, $this->localeModel->getRequiredFields())
            );
        }, ARRAY_FILTER_USE_KEY);
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $this->addError(new \WP_Error('missing_field', "Invalid value for field {$key}"));
                return false;
            }
        }
        $data[$this->localeModel->getIdColumn()] = $id;
        return $data;
    }
    public function updateLocale(int $id, array $data)
    {
        $locale = $this->buildLocaleUpdateData($id, $data);
        if (!$locale) {
            $this->addError(new \WP_Error('update_error', 'Update data is invalid'));
            return false;
        }

        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->update($locale);
    }

    public function deleteLocale($data)
    {
        $this->setWhereQueryConditions($this->defaultWhereConditions());
        return $this->deleteBatchData($data);
    }

}
