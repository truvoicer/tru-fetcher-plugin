<?php

namespace TruFetcher\Includes\DB\Model;

use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine_Base;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\WP\Tru_Fetcher_DB_Model_WP;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

class Tru_Fetcher_DB_Model
{

    public const DATE_UPDATED_COLUMN = 'date_updated';
    public const DATE_CREATED_COLUMN = 'date_created';
    public const DEFAULT_SORT_ORDER = Tru_Fetcher_DB_Model_Constants::SORT_ORDER_DESC;
	protected $tablePrefix;
	protected $charsetCollate;

	public string $tableName;
	protected array $tableConfig;

	private array $integerDataTypes = [
		'mediumint',
		'int',
		'bigint'
	];
	private array $stringDataTypes = [
		'varchar',
		'longtext',
		'mediumtext',
	];
	private array $booleanDataTypes = [
		'tinyint',
	];

    protected array $serializedFields = [];
    protected array $requiredFields = [];

	private static array $reservedFieldKeys = [
		Tru_Fetcher_DB_Model_Constants::CONDITIONS_KEY,
		Tru_Fetcher_DB_Model_Constants::DATA_KEY,
		Tru_Fetcher_DB_Model_Constants::WHERE_GROUP_CONDITION_KEY,
		Tru_Fetcher_DB_Model_Constants::WHERE_GROUP_CONDITION_DEFAULT,
		Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_KEY,
		Tru_Fetcher_DB_Model_Constants::DEFAULT_WHERE_COMPARE,
		Tru_Fetcher_DB_Model_Constants::DEFAULT_WHERE_LOGICAL_OPERATOR,
		Tru_Fetcher_DB_Model_Constants::WHERE_COLUMN_CONDITION_KEY,
	];

	protected int $id;
	protected string $idColumn = 'id';
	protected string $dateUpdatedColumn = self::DATE_UPDATED_COLUMN;
	protected string $dateCreatedColumn = self::DATE_CREATED_COLUMN;

    private string $dateInsertCond = "datetime DEFAULT '0000-00-00 00:00:00' NOT NULL";

    protected bool $dateInserts = true;

    protected ?string $orderDir = null;
    protected ?array $orderBy = [];


	public function __construct()
	{
		global $wpdb;
		$this->tablePrefix = $wpdb->prefix;
		$this->charsetCollate = $wpdb->get_charset_collate();
	}

	/**
	 * @return int
	 */
	public function getId(): int
	{
		return $this->id;
	}

	/**
	 * @param int $id
	 */
	public function setId(int $id): void
	{
		$this->id = $id;
	}

	/**
	 * @param string $tableName
	 */
	public function setTableName(string $tableName): void
	{
		$this->tableName = $tableName;
	}

	/**
	 * @return string
	 */
	public function getTableName(?\WP_Site $site = null, ?bool $isNetworkWide = false): string
	{
        if (!$site || $site->blog_id === '1') {
            return $this->tablePrefix . $this->tableName;
        }
        if ($isNetworkWide) {
            return "{$this->tablePrefix}{$site->blog_id}_{$this->tableName}";
        }
        return "{$this->tablePrefix}{$this->tableName}";
	}


	/**
	 * @return string
	 */
	public function getDbPlaceholderByColumn(string $column): string
	{
        if (str_contains($column, '.')) {
            $column = explode('.', $column)[1];
        }
        $columns = $this->getColumns();
        if (!$columns) {
            return false;
        }
        if (!in_array($column, array_keys($columns))) {
            return false;
        }
        $columnDataType = $columns[$column];
        if ($this->isDataType($this->booleanDataTypes, $columnDataType)) {
            return '%d';
        }
        if ($this->isDataType($this->integerDataTypes, $columnDataType)) {
            return '%d';
        }
        return '%s';
	}

	/**
	 * @return array
	 */
	public function getTableConfig(): array
	{
		return $this->tableConfig;
	}

	/**
	 * @param array $tableConfig
	 */
	public function setTableConfig(array $tableConfig): void
	{
		$this->tableConfig = $tableConfig;
	}

	public function existsInString(array $data, string $string)
	{
		foreach ($data as $value) {
			if (strpos($string, $value) !== false) {
				return true;
			}
		}
		return false;
	}

	public function isDataType(array $dataTypes, $columnDataType)
	{
		return $this->existsInString(
			array_map(function ($value) {
				return "{$value}";
			}, $dataTypes),
			$columnDataType
		);
	}

	public function buildModelData(array $data)
	{
		$columns = $this->getColumns();
		if (!$columns) {
			return false;
		}
		$buildData = [];
		foreach ($data as $key => $value) {
			if (!array_key_exists($key, $columns)) {
				$buildData[$key] = $value;
				continue;
			}
			$columnDataType = $columns[$key];
            if (is_null($value)) {
                $buildData[$key] = null;
                continue;
            }
            if ($this->isDataType($this->booleanDataTypes, $columnDataType)) {
                $buildData[$key] = (bool)$value;
                continue;
            }
			if ($this->isDataType($this->integerDataTypes, $columnDataType)) {
				$buildData[$key] = (int)$value;
                continue;
			}
			if ($this->isDataType($this->stringDataTypes, $columnDataType)) {
                if (in_array($key, $this->serializedFields)) {
                    if (!is_string($value)) {
                        $buildData[$key] = $value;
                        continue;
                    }
                    $buildData[$key] = unserialize(str_replace('\\', '', $value));
                    if (!is_array($buildData[$key])) {
                        $buildData[$key] = [];
                    }
                } else {
                    $buildData[$key] = (string)$value;
                }
                continue;
			}
			if (
				strpos($columnDataType, 'NOT NULL') === false &&
				strpos($columnDataType, 'NULL') !== false
			) {
				$buildData[$key] = null;
                continue;
			}
			if (!isset($buildData[$key])) {
//                var_dump(['oth', $key]);
				$buildData[$key] = $value;
			}
        }
		return $buildData;
	}

	public function buildModelDataBatch(array $data)
	{
		return array_map(function ($item) {
			return $this->buildModelData($item) ?? [];
		}, $data);
	}

	public static function removeReservedFieldKeys(array $data)
	{
		foreach ($data as $key => $value) {
			if (in_array($key, self::$reservedFieldKeys)) {
				unset($data[$key]);
			}
		}
		return $data;
	}

	public static function removeReservedFieldKeysFromBatch(array $data)
	{
		return array_map(function ($item) {
			return self::removeReservedFieldKeys($item);
		}, $data);
	}

	public function buildFieldArray(array $data, array $required)
	{
		return array_filter($data, function ($val, $key) use ($required) {
			return (
				array_key_exists($key, $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::COLUMNS]) ||
				in_array($key, self::$reservedFieldKeys)
			);
		}, ARRAY_FILTER_USE_BOTH);
	}

	public function validateFields(array $data, array $required = [], bool $mustHaveAllFields = true)
	{
		$tableColumnData = $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::COLUMNS];
		unset($tableColumnData['id']);
		$buildFields = $this->buildFieldArray($data, $required);
		$compare = (count($buildFields) === count($tableColumnData));

		if ($mustHaveAllFields) {
			return $buildFields;
		} elseif (!count($required) && !$compare) {
			return $this->returnValidationWpError(
				array_diff_key($tableColumnData, $buildFields)
			);
		} elseif (!$compare) {
			return $this->returnValidationWpError(
				array_diff(array_keys($buildFields), $required)
			);
		} elseif ($compare) {
			return $buildFields;
		}
		return $this->returnValidationWpError($data);
	}

	public function validateFieldBatch($data, array $required = [], bool $mustHaveAllFields = true)
	{
		return array_map(function ($item) use ($required) {
			return $this->validateFields($item, $required);
		}, $data);
	}

	protected function returnValidationWpError(
		$data = [],
		$message = 'Validation failed, missing required columns',
		$errorCode = Tru_Fetcher_DB_Model_Constants::ERROR_CODE_PREFIX . '_validation_failed'
	)
	{
		return new \WP_Error(
			$errorCode,
			$message,
			$data
		);
	}


	public static function getForeignKeyValues(Tru_Fetcher_DB_Model $model, $key)
	{
		return array_map(function ($item) use ($key) {
			return $item[$key];
		}, $model->getTableConfig()[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD]);
	}

	public static function removeModelFromArray($model, array $modelArray, ?string $modelFieldName = null)
	{
		$findModelIndex = Tru_Fetcher_DB_Model::findModelIndexInArray(
			$model,
			$modelArray,
			$modelFieldName
		);
		if ($findModelIndex !== false) {
			unset($modelArray[$findModelIndex]);
		}
		return array_values($modelArray);
	}

	public static function findModelIndexInArray($model, array $modelArray, ?string $modelFieldName = null)
	{
		foreach ($modelArray as $index => $modelItem) {
			if (is_string($modelFieldName)) {
				if (get_class($model) !== get_class($modelItem[$modelFieldName])) {
					continue;
				}
				return $index;
			} else {
				if (get_class($model) === get_class($modelItem)) {
					return $index;
				}
			}
		}
		return false;
	}

	public static function findModelInArray($model, array $modelArray, ?string $modelFieldName = null)
	{
		$filterModels = array_filter($modelArray, function ($modelItem) use ($model, $modelFieldName) {
			if (is_string($modelFieldName)) {
				return (get_class($model) === get_class($modelItem[$modelFieldName]));
			}
			return (get_class($model) === get_class($modelItem));
		}, ARRAY_FILTER_USE_BOTH);
		return (count($filterModels)) ? $filterModels[array_key_first($filterModels)] : false;
	}

	public static function debugModelArray($data)
	{
		if (gettype($data) === 'object') {
			return get_class($data);
		}
		foreach ($data as $key => $item) {
			if ($item instanceof Tru_Fetcher_DB_Model) {
				$data[$key] = $item->getTableName();
				continue;
			}
			if (gettype($item) === 'object') {
				$data[$key] = get_class($item);
				continue;
			}
			if (is_array($item)) {
				$data[$key] = self::debugModelArray($item);
			}
		}
		return $data;
	}

	public static function isValidModel($model)
	{
		return ($model instanceof Tru_Fetcher_DB_Model || $model instanceof Tru_Fetcher_DB_Model_WP);
	}

	public static function getModelForeignKeys(Tru_Fetcher_DB_Model $model)
	{
		$cloneModel = new $model;
		$foreignKeys = $cloneModel->getForeignKeys();
		if (!$foreignKeys) {
			return false;
		}
		return $foreignKeys;
	}
	public function getForeignKeys()
	{
		if (isset($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD])) {
			return $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD];
		}
		return false;
	}

	public function getUniqueConstraints(): bool|array
	{
		if (
            isset($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::UNIQUE_CONSTRAINT_FIELD]) &&
            is_array($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::UNIQUE_CONSTRAINT_FIELD])
        ) {
			return $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::UNIQUE_CONSTRAINT_FIELD];
		}
		return false;
	}

	public function setForeignKeys(array $foreignKeys)
	{
		return $this->tableConfig[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEYS_FIELD] = $foreignKeys;
	}

	public function getPrimaryKey()
	{
		return $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::PRIMARY_KEY_FIELD];
	}

	public function getPivots()
	{
		if (
            !isset($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::PIVOTS]) ||
            !is_array($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::PIVOTS])
        ) {
            return false;
		}
        return $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::PIVOTS];
	}
	public function getColumns()
	{
		if (!isset($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::COLUMNS])) {
            return false;
		}
        $columns = $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::COLUMNS];
        if ($this->isDateInserts()) {
            $columns[$this->getDateUpdatedColumn()] = $this->dateInsertCond;
            $columns[$this->getDateCreatedColumn()] = $this->dateInsertCond;
        }
        return $columns;
	}

	public function getTableColumns()
	{
		if (!isset($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::COLUMNS])) {
            return false;
		}
        $columnKeys = array_keys($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::COLUMNS]);
        if ($this->isDateInserts()) {
            $columnKeys[] = $this->getDateUpdatedColumn();
            $columnKeys[] = $this->getDateCreatedColumn();
        }
        return $columnKeys;
	}

	public function hasColumns(array $columns)
	{
		$modelColumns = $this->getColumns();
		if (!$modelColumns || !is_array($modelColumns)) {
			return false;
		}
		foreach ($columns as $column) {
			if (!in_array($column, array_keys($modelColumns))) {
				return false;
			}
		}
		return true;
	}

	public function getAlias()
	{
		if (isset($this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::ALIAS])) {
			return $this->getTableConfig()[Tru_Fetcher_DB_Model_Constants::ALIAS];
		}
		return $this->getTableName();
	}

	public function findWpForeignKeys(?array $foreignKeys = null)
	{
		if (!$foreignKeys) {
			$foreignKeys = $this->getForeignKeys();
		}
		if (!$foreignKeys) {
			return false;
		}
		$foundForeignKeys = [];
		foreach ($foreignKeys as $foreignKey) {
			if (
				isset($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_WP_REFERENCE]) &&
				$foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_WP_REFERENCE]
			) {
				$foundForeignKeys[] = $foreignKey;
			}
		}
		return $foundForeignKeys;
	}

	public function findWpForeignKeysColumnsBatch(?array $wpForeignKeys = null)
	{
		if (!$wpForeignKeys) {
			$wpForeignKeys = $this->findWpForeignKeys();
		}
		if (!$wpForeignKeys) {
			return false;
		}
		$wpForeignKeyColumns = [];
		foreach ($wpForeignKeys as $foreignKey) {
			$findFields = $this->findWpForeignKeysColumns($foreignKey);
			if (!$findFields) {
				continue;
			}
			$wpForeignKeyColumns = array_merge($wpForeignKeyColumns, $findFields);
		}
		return $wpForeignKeyColumns;
	}

	public function findWpForeignKeysColumns(array $wpForeignKey = null)
	{
		if (!isset($wpForeignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN])) {
			return false;
		}
		$targetColumns = $wpForeignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN];
		if (!is_array($targetColumns)) {
			return false;
		}
		return $targetColumns;
	}


	public static function findForeignKeyByReferenceModel($targetModel, array $foreignKeys, $returnIndex = false)
	{
		foreach ($foreignKeys as $index => $foreignKeyItem) {
			if (get_class($targetModel) === get_class($foreignKeyItem[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE][Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL])) {
				return ($returnIndex)? $index : $foreignKeyItem;
			}
		}
		return false;
	}


	public static function getForeignKeyRelation(array $foreignKey)
	{
		if (
			!isset($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_RELATION]) ||
			Tru_Fetcher_Helpers::isArrayEmpty($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_RELATION])
		) {
			return false;
		}
		return $foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_RELATION];
	}

	public static function getForeignKeyReference(array $foreignKey) {
		if (!isset($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE])) {
			return false;
		}
		return $foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE];
	}
	public static function getForeignKeyReferenceModel(array $foreignKey) {
		$foreignKeyReference = self::getForeignKeyReference($foreignKey);
		if (!$foreignKeyReference || !isset($foreignKeyReference[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL])) {
			return false;
		}
		return $foreignKeyReference[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL];
	}
	public static function getForeignKeyReferenceColumn(array $foreignKey) {
		$foreignKeyReference = self::getForeignKeyReference($foreignKey);
		if (!$foreignKeyReference || !isset($foreignKeyReference[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN])) {
			return false;
		}
		return $foreignKeyReference[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN];
	}

	public static function getForeignKeyTargetColumn(array $foreignKey) {
		if (!isset($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN])) {
			return false;
		}
		return $foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN];
	}


	public static function getModelInArray(array $data) {
		if (!isset($data[Tru_Fetcher_DB_Model_Constants::MODEL_KEY])) {
			return false;
		}
		$modelParent = get_parent_class($data[Tru_Fetcher_DB_Model_Constants::MODEL_KEY]);
		if (
			$modelParent !== Tru_Fetcher_DB_Model_WP::class &&
			$modelParent !== self::class
		) {
			return false;
		}
		return $data[Tru_Fetcher_DB_Model_Constants::MODEL_KEY];
	}

	/**
	 * @return string
	 */
	public function getIdColumn(): string
	{
		return $this->idColumn;
	}

	/**
	 * @param string $idColumn
	 */
	public function setIdColumn(string $idColumn): void
	{
		$this->idColumn = $idColumn;
	}

	/**
	 * @return string
	 */
	public function getDateUpdatedColumn(): string
	{
		return $this->dateUpdatedColumn;
	}

	/**
	 * @param string $dateUpdatedColumn
	 */
	public function setDateUpdatedColumn(string $dateUpdatedColumn): void
	{
		$this->dateUpdatedColumn = $dateUpdatedColumn;
	}

	/**
	 * @return string
	 */
	public function getDateCreatedColumn(): string
	{
		return $this->dateCreatedColumn;
	}

	/**
	 * @param string $dateCreatedColumn
	 */
	public function setDateCreatedColumn(string $dateCreatedColumn): void
	{
		$this->dateCreatedColumn = $dateCreatedColumn;
	}

	/**
	 * @return array
	 */
	public static function getReservedFieldKeys(): array
	{
		return self::$reservedFieldKeys;
	}

    /**
     * @return bool
     */
    public function isDateInserts(): bool
    {
        return $this->dateInserts;
    }

    public function addDateInserts(array $columns)
    {
        if (!$this->isDateInserts()) {
            return $columns;
        }
        $columns[] = "{$this->getDateUpdatedColumn()} {$this->dateInsertCond}";
        $columns[] = "{$this->getDateCreatedColumn()} {$this->dateInsertCond}";
        return $columns;
    }
    public function addDateQueryData(array $data, string $operation)
    {
        if (!$this->isDateInserts()) {
            return $data;
        }
        $nowDate = \wp_date('Y-m-d H:i:s');
        switch ($operation) {
            case Tru_Fetcher_DB_Engine_Base::DB_OPERATION_INSERT:
                $data[$this->getDateUpdatedColumn()] = $nowDate;
                $data[$this->getDateCreatedColumn()] = $nowDate;
                break;
            case Tru_Fetcher_DB_Engine_Base::DB_OPERATION_UPDATE:
                $data[$this->getDateUpdatedColumn()] = $nowDate;
                break;
        }
        return $data;
    }

    public function getFullColumnName(string $column)
    {
        return "{$this->getTableName()}.{$column}";
    }

    /**
     * @return array
     */
    public function getRequiredFields(): array
    {
        return $this->requiredFields;
    }

    /**
     * @param array $requiredFields
     */
    public function setRequiredFields(array $requiredFields): void
    {
        $this->requiredFields = $requiredFields;
    }

    /**
     * @return string|null
     */
    public function getOrderDir(): ?string
    {
        return $this->orderDir;
    }

    /**
     * @param string|null $orderDir
     */
    public function setOrderDir(?string $orderDir): void
    {
        $this->orderDir = $orderDir;
    }

    /**
     * @return array|null
     */
    public function getOrderBy(): ?array
    {
        return $this->orderBy;
    }

    /**
     * @param array|null $orderBy
     */
    public function setOrderBy(?array $orderBy = null): void
    {
        $this->orderBy = $orderBy;
    }

}
