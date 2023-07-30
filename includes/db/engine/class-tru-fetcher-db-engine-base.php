<?php

namespace TruFetcher\Includes\DB\Engine;

use TruFetcher\Includes\DB\data\Tru_Fetcher_DB_Data_Form_Preset;
use TruFetcher\Includes\DB\data\Tru_Fetcher_DB_Data_Settings;
use TruFetcher\Includes\DB\data\Tru_Fetcher_DB_Data_Tab_Preset;
use TruFetcher\Includes\DB\data\Tru_Fetcher_DB_Data_Topic;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Api_Tokens;

use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device_Topic;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Device;


use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Form_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Post_Meta;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Ratings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Saved_Items;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Settings;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Tab_Presets;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_Topic;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model_User_Device_Access;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;

class Tru_Fetcher_DB_Engine_Base
{
    use Tru_Fetcher_DB_Traits_WP_Site;

	const DB_VERSION = "1.0";
	public const DB_OPERATION_INSERT = 'insert';
	public const DB_OPERATION_UPDATE = 'update';
	public const DB_OPERATION_DELETE = 'delete';
	public const DB_OPERATION_KEY = 'db_operation';
	public const IS_NULL = 'IS_NULL';

	public const RESULTS_FORMAT_OBJECT = 'RESULTS_FORMAT_OBJECT';

	protected Tru_Fetcher_DB_Model $model;

	protected $tablePrefix;
	protected $charsetCollate;


    private string $resultsFormat = self::RESULTS_FORMAT_OBJECT;

	public function __construct()
	{
		global $wpdb;
		$this->tablePrefix = $wpdb->prefix;
		$this->charsetCollate = $wpdb->get_charset_collate();
        $this->initialise();
	}


	public function dbExec($sql)
	{
		require_once(get_home_path() . '/wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

	public function adminNoticeMissingTable(array $data = [])
	{
        if (is_network_admin() && is_multisite()) {
            self::renderAdminNoticeMissingTable('network_admin_notices', $data);
        } else {
            self::renderAdminNoticeMissingTable('admin_notices', $data);
        }
    }

	public static function renderAdminNoticeMissingTable(string $notice, array $data = [])
	{
        $data['notice'] = $notice;
		add_action($notice, function () use ($data) {
            if (isset($data['tables'])) {
                require_once(TRU_FETCHER_PLUGIN_DIR . 'includes/db/templates/missing-tables.php');
            } else if (isset($data['missing_columns'])) {
                require_once(TRU_FETCHER_PLUGIN_DIR . 'includes/db/templates/missing-columns.php');
            } else if (isset($data['required_data'])) {
                require_once(TRU_FETCHER_PLUGIN_DIR . 'includes/db/templates/missing-required-data.php');
            }
		});
	}

    private function buildTableName(Tru_Fetcher_DB_Model $model) {
        return $model->getTableName($this->site, $this->isNetworkWide);
    }

    public function checkAllMissingTables()
    {
        $failed = [];
        if ($this->isNetworkWide && $this->isMultiSite) {
            foreach (get_sites() as $site) {
                $this->setSite($site);
                $failed = array_merge($failed, $this->checkMissingTables());
            }
        } elseif (!$this->isNetworkWide && $this->isMultiSite) {
            $this->setSite(get_site());
            $failed = array_merge($failed, $this->checkMissingTables());
        } else {
            $this->setSite(null);
            $failed = array_merge($failed, $this->checkMissingTables());
        }

        if (!empty($failed)) {
            return new \WP_Error(
                'tru_fetcher_error_health_check',
                'Table error',
                $failed
            );
        }
        return true;
    }

    public function checkMissingTables()
    {
        $getTables = Tru_Fetcher_DB_Engine_Base::getTables();
        $dbTables = $this->getDbTables($getTables);
        $filterTables = array_filter($getTables, function (Tru_Fetcher_DB_Model $model, $key) use ($dbTables) {
            return !in_array($model->getTableName($this->site, $this->isNetworkWide), $dbTables);
        }, ARRAY_FILTER_USE_BOTH);
        return array_map(function (Tru_Fetcher_DB_Model $model) {
            return $model->getTableName($this->site, $this->isNetworkWide);
        }, $filterTables);
    }

    private function createRequiredTables() {
        $results = [];
        foreach (Tru_Fetcher_DB_Engine_Base::getTables() as $requiredTable) {
            $results[] = [
                Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $requiredTable->getTableName($this->site, $this->isNetworkWide),
                'result' => $this->createTable($requiredTable)
            ];
        }
        return $results;
    }

	public function installRequiredTables()
	{
		global $wpdb;
		$wpdb->hide_errors();

		$results = [];
		try {
            if ($this->isNetworkWide && $this->isMultiSite) {
                foreach (get_sites() as $site) {
                    $this->setSite($site);
                    $results = array_merge($results, $this->createRequiredTables());
                }
            } elseif (!$this->isNetworkWide && $this->isMultiSite) {
                $this->setSite(get_site());
                $results = $this->createRequiredTables();
            } else {
                $this->setSite(null);
                $results = $this->createRequiredTables();
            }
		} catch (\Exception $e) {
            $errorData = [
                Tru_Fetcher_DB_Model_Constants::MODEL_KEY => false,
                'result' => [
                    'status' => 'error',
                    'code' => $e->getCode(),
                    'message' => $e->getMessage(),
                ]
            ];
			$results[] = $errorData;
            error_log(json_encode($errorData));
		}

		$wpdb->show_errors();
		return $results;
	}

    public function checkRequiredData()
    {
        $errors = [];
        if ($this->isNetworkWide && $this->isMultiSite) {
            foreach (get_sites() as $site) {
                $this->setSite($site);
                $compareColumns = $this->validateRequiredData();
                if (is_wp_error($compareColumns)) {
                   $errors = array_merge($errors, $compareColumns->get_error_data());
                }
            }
        } elseif (!$this->isNetworkWide && $this->isMultiSite) {
            $this->setSite(get_site());
            $compareColumns = $this->validateRequiredData();
            if (is_wp_error($compareColumns)) {
                $errors = array_merge($errors, $compareColumns->get_error_data());
            }
        } else {
            $this->setSite(null);
            $compareColumns = $this->validateRequiredData();
            if (is_wp_error($compareColumns)) {
                $errors = array_merge($errors, $compareColumns->get_error_data());
            }
        }
        if (count($errors)) {
            return new \WP_Error(
                'tru_fetcher_error_health_check',
                'Table error',
                $errors
            );
        }
        return true;
    }

    public function validateRequiredData()
    {
        $errors = [];
        foreach (Tru_Fetcher_DB_Engine_Base::getInitialTableData() as $dataClass) {
            if (!method_exists($dataClass, 'check')) {
                $errors[] = [
                    Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $dataClass->getEntityName(),
                    'errors' => ['Missing required data check function']
                ];
                continue;
            }
            $dataClass->setSite($this->site);
            $dataClass->setIsNetworkWide($this->isNetworkWide);
            $dataClass->setIsMultiSite($this->isMultiSite);
            $checkData = $dataClass->check();
            if (!isset($checkData['success'])) {
                $errors[] = [
                    Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $dataClass->getEntityName(),
                    'errors' => ['Invalid response for installing required data']
                ];
            }
            if (!$checkData['success']) {
                $errors[] = [
                    Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $dataClass->getEntityName(),
                    'errors' => $dataClass->getErrors()
                ];
            }

        }
        if (count($errors)) {
            return new \WP_Error(
                'tru_fetcher_error_health_check',
                'Table error',
                $errors
            );
        }
        return true;
    }


	public function installRequiredModelData(): array
    {
        $results = [];
        foreach (Tru_Fetcher_DB_Engine_Base::getInitialTableData() as $dataClass) {
            $dataClass->setSite($this->site);
            $dataClass->setIsNetworkWide($this->isNetworkWide);
            $dataClass->setIsMultiSite($this->isMultiSite);
            $results[] = [
                Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $dataClass->getEntityName(),
                'result' => $dataClass->install()
            ];
        }
        return $results;
    }
	public function buildInitialModelData()
	{
		global $wpdb;
		$wpdb->hide_errors();

		$results = [];
		try {
            if ($this->isNetworkWide && $this->isMultiSite) {
                foreach (get_sites() as $site) {
                    $this->setSite($site);
                    $results = array_merge($results, $this->installRequiredModelData());
                }
            } elseif (!$this->isNetworkWide && $this->isMultiSite) {
                $this->setSite(get_site());
                $results = array_merge($results, $this->installRequiredModelData());
            } else {
                $this->setSite(null);
                $results = array_merge($results, $this->installRequiredModelData());
            }
		} catch (\Exception $e) {
			$results[] = [
				Tru_Fetcher_DB_Model_Constants::MODEL_KEY => false,
				'result' => [
					'status' => 'error',
					'code' => $e->getCode(),
					'message' => $e->getMessage(),
				]
			];
		}

		$wpdb->show_errors();
		return $results;
	}

	public function dropTable(Tru_Fetcher_DB_Model $model)
	{
		global $wpdb;
		$getTable = $this->getTable($model);
		if ($getTable instanceof Tru_Fetcher_DB_Model) {
			$sql = "DROP TABLE IF EXISTS {$model->getTableName($this->site, $this->isNetworkWide)};";
			return $wpdb->query($sql);
		}
		return false;
	}


    public function compareModelColumns() {
        $errors = [];
        $results = [];
        foreach (self::getTables() as $model) {
            $columnCompare = $this->compareModelTableColumns($model);
            if (!is_array($columnCompare)) {
                $errors[] = [
                    Tru_Fetcher_DB_Model_Constants::MODEL_NAME_KEY => $model->getTableName($this->site, $this->isNetworkWide),
                    Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $model,
                    'errors' => ['Unknown error']
                ];
                continue;
            }
            if (count($columnCompare['new_columns']) || count($columnCompare['removed_columns'])) {
                $results[] = [
                    Tru_Fetcher_DB_Model_Constants::MODEL_NAME_KEY => $model->getTableName($this->site, $this->isNetworkWide),
                    Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $model,
                    'data' => $columnCompare,
                ];
            }
        }
        return [
            'errors' => $errors,
            'results' => $results,
        ];
    }

    public function checkMissingColumns()
    {
        $errors = [];
        $results = [];
        if ($this->isNetworkWide && $this->isMultiSite) {
            foreach (get_sites() as $site) {
                $this->setSite($site);
                $compareColumns = $this->compareModelColumns();
                $errors = array_merge($errors, $compareColumns['errors']);
                $results = array_merge($results, $compareColumns['results']);
            }
        } elseif (!$this->isNetworkWide && $this->isMultiSite) {
            $this->setSite(get_site());
            $compareColumns = $this->compareModelColumns();
            $errors = array_merge($errors, $compareColumns['errors']);
            $results = array_merge($results, $compareColumns['results']);
        } else {
            $this->setSite(null);
            $compareColumns = $this->compareModelColumns();
            $errors = array_merge($errors, $compareColumns['errors']);
            $results = array_merge($results, $compareColumns['results']);
        }
        return [
            'errors' => $errors,
            'results' => $results,
        ];
    }
    public function updateAllTableColumns() {
        $results = [];
        if ($this->isNetworkWide && $this->isMultiSite) {
            foreach (get_sites() as $site) {
                $this->setSite($site);
                $update = $this->updateModelColumns();
                if (is_wp_error($update)) {
                    return $update->get_error_data();
                }
                if ($update === true) {
                    continue;
                }
                $results = array_merge($results, $update);
            }
        } elseif (!$this->isNetworkWide && $this->isMultiSite) {
            $this->setSite(get_site());
            $update = $this->updateModelColumns();
            if (is_wp_error($update)) {
                return $update->get_error_data();
            }
            $results = array_merge($results, $update);
        } else {
            $this->setSite(null);
            $update = $this->updateModelColumns();
            if (is_wp_error($update)) {
                return $update->get_error_data();
            }
            $results = array_merge($results, $update);
        }
        return $results;
    }

    private function addTableColumnBatch(Tru_Fetcher_DB_Model $model, array $columns) {
        $errors = [];
        foreach ($columns as $column) {
            $addColumn = $this->updateTableColumn($model, $column, 'add');
            if (!isset($addColumn['success'])) {
                $errors[] = $column;
                continue;
            }
            if (!$addColumn['success']) {
                $errors[] = $column;
            }
        }
        if (count($errors)) {
            return $errors;
        }
        return true;
    }
    private function removeTableColumnBatch(Tru_Fetcher_DB_Model $model, array $columns) {
        $errors = [];
        foreach ($columns as $column) {
            $addColumn = $this->updateTableColumn($model, $column, 'remove');
            if (!isset($addColumn['success'])) {
                $errors[] = $column;
                continue;
            }
            if (!$addColumn['success']) {
                $errors[] = $column;
            }
        }
        if (count($errors)) {
            return $errors;
        }
        return true;
    }

    public function updateModelColumns() {
        $checkMissingColumns = $this->compareModelColumns();
        if (count($checkMissingColumns['errors'])) {
            return new \WP_Error(
                'tru_fetcher_error_health_check',
                'Errors checking missing table columns',
                [
                    'success' => false,
                    'errors' => $checkMissingColumns['errors']
                ]
            );
        }
        if (!count($checkMissingColumns['results'])) {
            return true;
        }
        $results = [];
        foreach ($checkMissingColumns['results'] as $result) {
            $model = $result[Tru_Fetcher_DB_Model_Constants::MODEL_KEY];
            $resultData = [
                Tru_Fetcher_DB_Model_Constants::MODEL_KEY => $model->getTableName($this->site, $this->isNetworkWide)
            ];
            if (isset($result['data']['new_columns']) && is_array($result['data']['new_columns'])) {
                $addTableColumnBatch = $this->addTableColumnBatch($model, $result['data']['new_columns']);
                if (is_array($addTableColumnBatch)) {
                    $resultData['result'] = [
                        'success' => false,
                        'message' => sprintf("Columns %s not added", implode(', ', $addTableColumnBatch))
                    ];
                }  else {
                    $resultData['result'] = [
                        'success' => true,
                        'message' => "Columns added"
                    ];
                }
                $results[] = $resultData;
            }
            if (isset($result['data']['removed_columns']) && is_array($result['data']['removed_columns'])) {
                $removeTableColumnBatch = $this->removeTableColumnBatch($model, $result['data']['removed_columns']);
                if (is_array($removeTableColumnBatch)) {
                    $resultData['result'] = [
                        'success' => false,
                        'message' => sprintf("Columns %s not removed", implode(', ', $removeTableColumnBatch))
                    ];
                } else {
                    $resultData['result'] = [
                        'success' => true,
                        'message' => "Columns removed"
                    ];
                }
                $results[] = $resultData;
            }
        }
        return $results;
    }
    public function removeTableColumn(Tru_Fetcher_DB_Model $model, string $column) {
        $dbName = DB_NAME;
        $sql = "ALTER TABLE {$dbName}.{$model->getTableName($this->site, $this->isNetworkWide)} DROP COLUMN {$column};";
        return $sql;
    }

    public function addTableColumn(Tru_Fetcher_DB_Model $model, string $column) {
        $modelColumns = $model->getColumns();
        if (!$modelColumns) {
            return [
                'success' => false,
                'message' => 'Model has no columns'
            ];
        }
        if (!isset($modelColumns[$column])) {
            return [
                'success' => false,
                'message' => "Column {$column} not found in model columns"
            ];
        }
        $dbName = DB_NAME;
        $columnOptions = $modelColumns[$column];
        $columnPos = array_search($column, array_keys($modelColumns));

        unset($modelColumns[$column]);
        $modelColumnKeys = array_keys($modelColumns);
        $posField = '';
        $position = 'AFTER';
        if ($columnPos === false || $columnPos > count($modelColumns)) {
            $posField = $modelColumnKeys[array_key_last($modelColumnKeys)];
        } elseif ($columnPos === 0) {
            $position = 'FIRST';
        } else {
            $posField = $modelColumnKeys[$columnPos - 1];
        }

        $sql = "ALTER TABLE {$dbName}.{$model->getTableName($this->site, $this->isNetworkWide)} ADD COLUMN {$column} {$columnOptions} {$position} {$posField}";

        $modelFks = $model->getForeignKeys();
        if (is_array($modelFks) && count($modelFks)) {
            $columnChildForeignKey = array_filter($modelFks, function ($fk) use($column) {
                $targetColKey = Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN;
                return (isset($fk[$targetColKey]) && $fk[$targetColKey] === $column);
            }, ARRAY_FILTER_USE_BOTH);
            if (count($columnChildForeignKey)) {
                foreach ($columnChildForeignKey as $foreignKey) {
                    $buildForeignKey = $this->buildForeignKey($foreignKey);
                    if (!$buildForeignKey) {
                        continue;
                    }
                    $sql .= " ADD {$buildForeignKey}";
                }
            }
        }
        $sql .= ';';
        return $sql;
    }

    public function updateTableColumn(Tru_Fetcher_DB_Model $model, string $column, string $operation) {
        global $wpdb;
        switch ($operation) {
            case 'add':
                $sql = $this->addTableColumn($model, $column);
                break;
            case 'remove':
                $sql = $this->removeTableColumn($model, $column);
                break;
        }
        $wpdb->query($sql);

        if ($wpdb->last_error !== '') {
            return [
                'success' => false,
                'message' => $wpdb->last_error
            ];
        }
        return [
            'success' => true
        ];
    }
    private function buildForeignKey(array $foreignKey) {
        if (isset($foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_WP_REFERENCE]) && $foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_WP_REFERENCE]) {
            return false;
        }
        $foreignKeyField = $foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_TARGET_COLUMN];
        $foreignKeyReference = $foreignKey[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE];
        $foreignKeyReferenceModel = $foreignKeyReference[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_MODEL];
        $foreignKeyReferenceColumn = $foreignKeyReference[Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_REFERENCE_COLUMN];
        $fkCascadeKey = Tru_Fetcher_DB_Model_Constants::FOREIGN_KEY_CASCADE_DELETE;
        $cascade = ' ON DELETE CASCADE';
        if (isset($key[$fkCascadeKey]) && !$key[$fkCascadeKey]) {
            $cascade = '';
        }
        return "FOREIGN KEY ({$foreignKeyField}) REFERENCES {$foreignKeyReferenceModel->getTableName($this->site, $this->isNetworkWide)}({$foreignKeyReferenceColumn}){$cascade}";
    }

	public function createTable(Tru_Fetcher_DB_Model $model)
	{
		global $wpdb;
		if (!isset($model->getTableConfig()[Tru_Fetcher_DB_Model_Constants::COLUMNS])) {
            return [
                'success' => false,
                'message' => 'Incorrect model setup'
            ];
		}

		$tableConfig = $model->getTableConfig();
		$primaryKey = $model->getPrimaryKey();
		$foreignKeys = $model->getForeignKeys();

		$sql = "CREATE TABLE IF NOT EXISTS {$model->getTableName($this->site, $this->isNetworkWide)} (";

		$dataTypes = array_map(function ($column) use ($tableConfig) {
			return "{$column} {$tableConfig[Tru_Fetcher_DB_Model_Constants::COLUMNS][$column]}";
		}, array_keys($tableConfig[Tru_Fetcher_DB_Model_Constants::COLUMNS]));
        $dataTypes = $model->addDateInserts($dataTypes);
		if (!empty($dataTypes)) {
			$sql .= implode(", ", $dataTypes);
		}
		if (isset($primaryKey)) {
			$sql .= ", PRIMARY KEY ({$primaryKey})";
		}
		if (is_array($foreignKeys)) {
			foreach ($foreignKeys as $key) {
                $buildForeignKey = $this->buildForeignKey($key);
                if (!$buildForeignKey) {
                    continue;
                }
				$sql .= ", {$buildForeignKey}";
			}
		}
		$sql .= ") $this->charsetCollate;";

		$wpdb->query($sql);

		if ($wpdb->last_error !== '') {
			return [
				'success' => false,
				'message' => $wpdb->last_error
			];
		}
		return [
			'success' => true
		];
	}

    public function findOne($query, $parameters, ?string $output = ARRAY_A)
    {
        global $wpdb;
        return $wpdb->get_row($wpdb->prepare($query, $parameters), $output);
    }

    public function findMany($query, $parameters, ?string $output = ARRAY_A)
    {
        global $wpdb;
        if (is_array($parameters) && count($parameters)) {
            return $wpdb->get_results($wpdb->prepare($query, $parameters), $output);
        }
        return $wpdb->get_results($query, $output);
    }

    public function query($query, $parameters)
    {
        global $wpdb;
        $query = $wpdb->query(
            $wpdb->prepare($query, $parameters)
        );
        return $query;
    }

    public function getRow(Tru_Fetcher_DB_Model $model, $where, $parameters, ?string $output = OBJECT)
    {
        global $wpdb;
        $query = "SELECT * FROM {$model->getTableName($this->site, $this->isNetworkWide)} WHERE $where";
        return $wpdb->get_row($wpdb->prepare($query, $parameters), $output);
    }

	public function getAllResultsRaw(Tru_Fetcher_DB_Model $model)
	{
		global $wpdb;
		$query = "SELECT * FROM {$model->getTableName($this->site, $this->isNetworkWide)}";
		$results = $wpdb->get_results($query);
		return $results;
	}

	public function getAllResults(Tru_Fetcher_DB_Model $model, ?string $output = OBJECT)
	{
		global $wpdb;
		$query = "SELECT * FROM {$model->getTableName($this->site, $this->isNetworkWide)}";
		$results = $wpdb->get_results($query, $output);
		return $results;
	}

    public function getSingleResult(Tru_Fetcher_DB_Model $model, $where, $parameters, ?string $output = OBJECT)
    {
        global $wpdb;
        $query = "SELECT * FROM {$model->getTableName($this->site, $this->isNetworkWide)} WHERE $where";
        return $wpdb->get_row($wpdb->prepare($query, $parameters), $output);
    }

	public function getResults(Tru_Fetcher_DB_Model $model, $where, $parameters, ?string $output = OBJECT)
	{
		global $wpdb;
		$query = "SELECT * FROM {$model->getTableName($this->site, $this->isNetworkWide)} WHERE $where";
		$results = $wpdb->get_results($wpdb->prepare($query, $parameters), $output);
		return (!is_array($results) || empty($results)) ? false : $results;
	}

	public function getQuery($query, ...$parameters)
	{
		global $wpdb;
		return $wpdb->get_results($wpdb->prepare($query, $parameters));
	}

	public function getCount(Tru_Fetcher_DB_Model $model, $label, $where, $parameters)
	{
		global $wpdb;
		$query = "SELECT count(id) as $label
				FROM {$model->getTableName($this->site, $this->isNetworkWide)}
				WHERE $where";
		return $wpdb->get_row($wpdb->prepare($query, $parameters));
	}

	public function getDbValue($value)
	{
        if ($value === self::IS_NULL) {
            return "NULL";
        } elseif (is_bool($value)) {
			return (int)$value;
		} elseif (is_integer($value)) {
			return $value;
		}
		global $wpdb;
		return "{$wpdb->_real_escape($value)}";
	}

	public function buildUpdateData(array $data)
	{
		$updateSetData = [];
		foreach ($data as $index => $value) {
            if (!isset($value)) {
                continue;
            }
            $dbValue = $this->getDbValue($value);
            if ($dbValue === 'NULL') {
                continue;
            }
			$updateSetData[$index] = $dbValue;
		}

		return $updateSetData;
	}
	public function filterUpdateData(array $data)
	{
		$updateSetData = [];
		foreach ($data as $index => $value) {
            if (!isset($value)) {
                continue;
            }
			$updateSetData[$index] = $this->getDbValue($value);
		}

		return $updateSetData;
	}
	public function buildUpdateSetArray(array $data)
	{
		$updateSetData = [];
		foreach ($data as $index => $value) {
            if ($value === 'NULL') {
                $updateSetData[] = "{$index} = NULL";
                continue;
            }
            $updateSetData[] = "{$index} = {$this->model->getDbPlaceholderByColumn($index)}";
		}

		return $updateSetData;
	}


	protected static function buildWhereArray(array $data)
	{
		$whereArray = [];
		foreach ($data as $column => $value) {
			$whereArray[] = "{$column}=%s";
		}
		return $whereArray;
	}

	public static function buildWhereString(array $data)
	{
		return implode(' AND ', Tru_Fetcher_DB_Engine_Base::buildWhereArray($data));
	}

	public function find(Tru_Fetcher_DB_Model $model, array $data, ?string $output = ARRAY_A)
	{
        $whereString = Tru_Fetcher_DB_Engine_Base::buildWhereString($data);
		$find = $this->getResults(
			$model,
			$whereString,
			array_values($data),
            $output
		);
		if (is_array($find) && !empty($find)) {
			return $find;
		}
		return false;
	}
	public function findRow(Tru_Fetcher_DB_Model $model, array $data, ?string $output = ARRAY_A)
	{
        $whereString = Tru_Fetcher_DB_Engine_Base::buildWhereString($data);
		$find = $this->getRow(
			$model,
			$whereString,
			array_values($data),
            $output
		);
		if (is_array($find) && !empty($find)) {
			return $find;
		}
		return false;
	}

    public function runBatchQuery($query, array $prepareArgs)
    {
        global $wpdb;
        try {
            if (WP_DEBUG) {
                $wpdb->show_errors(true);
            }
            if (!count($prepareArgs)) {
                $queryResult = $wpdb->query($query);
            } else {
                $queryResult = $wpdb->query($wpdb->prepare($query, $prepareArgs));
            }
            return $queryResult;
        } catch (\Exception $e) {
            return new \WP_Error(
                    $e->getCode(),
                    $e->getMessage()
            );
        }
    }

    public function processBatchInsertResults($data)
    {
        global $wpdb;
        if ($wpdb->rows_affected > 1) {
            $rangeArray = range($wpdb->insert_id, ($wpdb->insert_id + $wpdb->rows_affected - 1));
            foreach ($rangeArray as $key => $value) {
                $data[$key]['id'] = $value;
            }
            return $data;
        }
        $data['id'] = $wpdb->insert_id;
        return $data;
    }

	public function getDbTables(array $models = [])
	{
		$buildQueryInArray = array_map(function (Tru_Fetcher_DB_Model $model) {
			return "'" . strtolower($model->getTableName($this->site, $this->isNetworkWide)) . "'";
		}, $models);
		$inString = implode(",", $buildQueryInArray);
		$dbName = DB_NAME;
		global $wpdb;
		$query = "SELECT * 
                    FROM information_schema.tables 
                    WHERE table_schema = '{$dbName}'
                        AND table_name IN ({$inString})";
		return array_map(function ($item) {
			return $item->TABLE_NAME;
		}, $wpdb->get_results($query));
	}

    private function getTableColumnsQuery(Tru_Fetcher_DB_Model $model) {
        $dbName = DB_NAME;
        global $wpdb;
        $query = "SELECT COLUMN_NAME
                    FROM information_schema.columns
                    WHERE table_schema = '{$dbName}'
                    AND table_name = '{$model->getTableName($this->site, $this->isNetworkWide)}'";
        return $wpdb->get_results($query, ARRAY_A);
    }

	public function compareModelTableColumns(Tru_Fetcher_DB_Model $model)
	{
        $tableColumns = $this->getTableColumnsQuery($model);
        if (!$tableColumns) {
            return false;
        }
        $tableColumns = array_column($tableColumns, 'COLUMN_NAME');
        $modelColumns = $model->getColumns();
        if (!$modelColumns) {
            return false;
        }
        $modelColumns = array_keys($modelColumns);
        $newColumns = array_diff($modelColumns, $tableColumns);
        $removedColumns = array_diff($tableColumns, $modelColumns);
        return [
          'new_columns' => $newColumns,
          'removed_columns' => $removedColumns
        ];
	}



	public function updateVersion()
	{
		add_option('tru_fetcher_db_version', self::DB_VERSION);
	}

	/**
	 *
	 * /**
	 * /**
	 * @return array
	 */
	public static function getTables(): array
	{
		return [
			new Tru_Fetcher_DB_Model_Device(),
			new Tru_Fetcher_DB_Model_Topic(),
			new Tru_Fetcher_DB_Model_Device_Topic(),
			new Tru_Fetcher_DB_Model_User_Device_Access(),
			new Tru_Fetcher_DB_Model_Settings(),
            new Tru_Fetcher_DB_Model_Api_Tokens(),
            new Tru_Fetcher_DB_Model_Post_Meta(),
            new Tru_Fetcher_DB_Model_Saved_Items(),
            new Tru_Fetcher_DB_Model_Ratings(),
            new Tru_Fetcher_DB_Model_Form_Presets(),
            new Tru_Fetcher_DB_Model_Tab_Presets(),
		];
	}
	/**
	 *
	 * /**
	 * /**
	 * @return array
	 */
	public static function getInitialTableData(): array
	{
		return [
			new Tru_Fetcher_DB_Data_Settings(),
			new Tru_Fetcher_DB_Data_Topic(),
			new Tru_Fetcher_DB_Data_Tab_Preset(),
			new Tru_Fetcher_DB_Data_Form_Preset(),
		];
	}

	/**
	 *
	 */
	public function getTable(Tru_Fetcher_DB_Model $model)
	{
        $tables = self::getTables();
		$findTable = array_search($model, $tables);
		if ($findTable !== false) {
			return $tables[$findTable];
		}
		return false;
	}

	public function getTableName(string $tableName)
	{
		return $this->tablePrefix . $tableName;
	}



    /**
     * @return string
     */
    public function getResultsFormat(): string
    {
        return $this->resultsFormat;
    }

    /**
     * @param string $resultsFormat
     */
    public function setResultsFormat(string $resultsFormat): void
    {
        $this->resultsFormat = $resultsFormat;
    }

    /**
     * @return Tru_Fetcher_DB_Model
     */
    public function getModel(): Tru_Fetcher_DB_Model
    {
        return $this->model;
    }

    /**
     * @param Tru_Fetcher_DB_Model $model
     */
    public function setModel(Tru_Fetcher_DB_Model $model): void
    {
        $this->model = $model;
    }


}
