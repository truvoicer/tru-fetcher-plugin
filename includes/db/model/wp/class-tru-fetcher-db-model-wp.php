<?php

namespace TruFetcher\Includes\DB\Model\WP;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;

abstract class Tru_Fetcher_DB_Model_WP
{
    public const ALIAS = 'alias';
    public const FIELDS = 'fields';

    public const ERROR_PREFIX = 'tr_news_app_db_wp';

    protected const WP_RETURN_DATA_TYPE = 'wp_return_data_type';
    protected const REQUIRED_FIELDS = 'required_fields';
    protected const MUST_HAVE_EITHER_FIELDS = 'must_have_either_fields';
    protected const REL_COLUMN_NAME_PREFIX = '%s__';

    protected array $config = [];

   abstract public function getData(array $foreignKey, array $result);

    /**
     * @return array
     */
    public function getConfig(): array
    {
        return $this->config;
    }

    /**
     * @param array $config
     */
    public function setConfig(array $config): void
    {
        $this->config = $config;
    }

	public function validateDataType($data)
	{
		if (!isset($data)) {
			return false;
		}
		if (!isset($this->getConfig()[self::WP_RETURN_DATA_TYPE])) {
			return false;
		}
		$returnDataType = $this->getConfig()[self::WP_RETURN_DATA_TYPE];
		if (is_object($data)) {
			return (get_class($data) === $returnDataType);
		}
		return  false;
	}

    public function getFields() {
        if (isset($this->getConfig()[self::FIELDS])) {
            return $this->getConfig()[self::FIELDS];
        }
        return false;
    }

    protected function validateFields(array $foreignKey, array $result) {
        if (!isset($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN]) || !is_array($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN])) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_target_col_invalid',
                'Foreign key target columns is invalid',
            );
        }
        $config = $this->getConfig();
        if (!$config) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_config_invalid',
                'Invalid config',
            );
        }
        $resultColumns = $foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN];
        foreach ($resultColumns as $resultColumn) {
            if (!in_array($resultColumn, $this->getFields())) {
                return new \WP_Error(
                    self::ERROR_PREFIX . '_foreign_keys_model_mismatch',
                    'Foreign key fields on model does not match wp model fields ',
                );
            }
        }
        if (
            !isset($config[self::MUST_HAVE_EITHER_FIELDS]) ||
            !is_array($config[self::MUST_HAVE_EITHER_FIELDS]) ||
            !count($config[self::MUST_HAVE_EITHER_FIELDS])
        ) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_must_have_either_fields_invalid',
                'Muse have either fields is invalid',
            );
        }
        $mustHaveEitherFields = $config[self::MUST_HAVE_EITHER_FIELDS];
        $filterResult = array_filter(array_keys($result), function ($key) use($mustHaveEitherFields) {
            return (in_array($key, $mustHaveEitherFields));
        }, ARRAY_FILTER_USE_BOTH);
        if (!count($filterResult)) {
            return new \WP_Error(
                self::ERROR_PREFIX . '_must_have_either_fields_invalid',
                'Muse have either fields does not exist in results',
            );
        }

        foreach ($filterResult as $key => $field) {
            if (in_array($field, $config[self::REQUIRED_FIELDS])) {
                unset($filterResult[$key]);
            }
        }
        return $filterResult;
    }

    public function getAlias() {
        if (isset($this->getConfig()[self::ALIAS])) {
            return $this->getConfig()[self::ALIAS];
        }
        return false;
    }

	public function getRelationsColumnNamePrefix() {
		$tableName = $this->getAlias();
		if (!$tableName) {
			return false;
		}
		return sprintf(
			self::REL_COLUMN_NAME_PREFIX,
			$tableName
		);
	}

}
