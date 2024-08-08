<?php

namespace TruFetcher\Includes\DB\Repository;


use TruFetcher\Includes\DB\Engine\Tru_Fetcher_DB_Engine;
use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\DB\Traits\WP\Tru_Fetcher_DB_Traits_WP_Site;
use TruFetcher\Includes\Traits\Tru_Fetcher_Traits_Errors;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

class Tru_Fetcher_DB_Repository_Base
{
    use Tru_Fetcher_DB_Traits_WP_Site, Tru_Fetcher_Traits_Errors;

    const DEFAULT_DATETIME_FORMAT = 'Y-m-d H:i:s';

    protected Tru_Fetcher_DB_Engine $db;

    protected Tru_Fetcher_DB_Model $model;

    protected array $select = [];

    protected array $whereGroups = [];
    protected array $where = [];

    protected array $orderBy = [];
    protected string $orderByDir = 'desc';

    protected array $groupBy = [];

    protected array $joins = [];

    protected ?int $limit = null;

    protected ?int $offset = null;

    protected array $values = [];
    protected array $whereQueryConditions = [];

    public function __construct(Tru_Fetcher_DB_Model $model)
    {
        $this->db = new Tru_Fetcher_DB_Engine();
        $this->model = $model;
        $this->initialise();
    }

    public static function getInstance(Tru_Fetcher_DB_Model $model)
    {
        return new static($model);
    }

    protected function escapeString($data): string
    {
        return serialize($data);
    }

    protected function defaultWhereConditions()
    {
        return [
            [
                Tru_Fetcher_DB_Model_Constants::FIELD_KEY => $this->model->getPrimaryKey(),
                Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_KEY => Tru_Fetcher_DB_Model_Constants::DEFAULT_WHERE_COMPARE,
                Tru_Fetcher_DB_Model_Constants::DATA_TYPE_KEY => Tru_Fetcher_DB_Model_Constants::DATA_TYPE_INT,
            ],
        ];
    }

    private function cleanup()
    {
        $this->select = [];
        $this->where = [];
        $this->orderBy = [];
        $this->orderByDir = 'desc';
        $this->groupBy = [];
        $this->joins = [];
        $this->limit = null;
        $this->offset = null;
        $this->values = [];
        $this->whereQueryConditions = [];
    }

    public function findById(int $id)
    {
        $this->addWhere($this->model->getIdColumn(), $id);
        $results = $this->db->findOne($this->buildQuery(), $this->values);
        $this->cleanup();
        if ($results) {
            return $this->model->buildModelData($results);
        }

        return $results;
    }

    public function findOne()
    {
        $results = $this->db->findOne($this->buildQuery(), $this->values);
        $this->cleanup();
        if ($results) {
            return $this->model->buildModelData($results);
        }
        return $results;
    }

    public function findMany()
    {
        $results = $this->db->findMany($this->buildQuery(), $this->values);
        $this->cleanup();
        if (!count($results)) {
            return [];
        }
        return $this->model->buildModelDataBatch($results);
    }

    private function buildSyncDataIds(string $key, array $data)
    {
        $filter = array_filter($data, function ($item) use ($key) {
            return isset($item[$key]);
        }, ARRAY_FILTER_USE_BOTH);
        return array_map(function ($item) use ($key) {
            return $item[$key];
        }, $filter);
    }

    public function sync(Tru_Fetcher_DB_Model $pivotModel, array $data)
    {
        $pivots = $this->model->getPivots();
        if (!$pivots || !count($pivots)) {
            return false;
        }
        $findPivotIndex = array_search($pivotModel::class, array_column($pivots, Tru_Fetcher_DB_Model_Constants::PIVOTS_TABLE));
        if ($findPivotIndex === false) {
            return false;
        }
        $pivot = $pivots[$findPivotIndex];
        $pivotForeignKey = $pivot[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY];
        $pivotForeignKeyReference = $pivot[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY_REFERENCE];
        $pivotForeignKeyReferenceModel = $pivot[Tru_Fetcher_DB_Model_Constants::PIVOTS_TABLE];
        $pivotForeignTable = $pivot[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_TABLE];
        $pivotRelatedTable = $pivot[Tru_Fetcher_DB_Model_Constants::PIVOT_RELATED_TABLE];
        $pivotRelatedKey = $pivot[Tru_Fetcher_DB_Model_Constants::PIVOT_RELATED_KEY];
        $pivotRelatedRef = $pivot[Tru_Fetcher_DB_Model_Constants::PIVOT_RELATED_REF];


        $foreignIds = $this->buildSyncDataIds($pivotForeignKey, $data);
        $relatedIds = $this->buildSyncDataIds($pivotRelatedKey, $data);

        $pivotForeignKeyReferenceModelInstance = new $pivotForeignKeyReferenceModel();
        $pivotForeignTableInstance = new $pivotForeignTable();
        $pivotRelatedTableInstance = new $pivotRelatedTable();

        $whereData = [];
        foreach ($data as $key => $item) {
            if (!isset($item[$pivotForeignKeyReference])) {
                continue;
            }
            if (!isset($item[$pivotRelatedKey])) {
                continue;
            }
            $whereData[] = $this->prepareWheredata(
                $key,
                $item,
                Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_EQUALS
            );
        }

        $pivotForeignKeyReferenceModelInstance->addWhereGroup($whereData);

        $results = $pivotForeignKeyReferenceModelInstance->findMany();
        if (count($results)) {
            $pivotForeignKeyReferenceModelInstance->deleteMany($results);
        }
        $data = array_filter($data, function ($item) use ($results, $pivotForeignKeyReference, $pivotRelatedKey) {
             return Tru_Fetcher_Helpers::findInArray(
                [
                    $pivotRelatedKey => $item[$pivotRelatedKey],
                    $pivotForeignKeyReference => $item[$pivotForeignKeyReference]
                ],
                $results,
                true
            ) === false;
        }, ARRAY_FILTER_USE_BOTH);

        $results = [];
        foreach ($data as $item) {
            $results[] = $instance->insert($item);
        }
        return $results;

    }

    public function deleteById(int $id)
    {
        $this->addWhere($this->model->getIdColumn(), $id);
        $findSavedItem = $this->findOne();
        if (!$findSavedItem) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_delete_error',
                    "{$this->model->getAlias()}: item id ({$id}) to delete not found",
                    [
                        'id' => $id,
                        'table' => $this->model->getTableName(),
                    ]
                )
            );
            return false;
        }

        $this->addWhere($this->model->getIdColumn(), $id);
        if (!$this->delete()) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_delete_error',
                    "{$this->model->getAlias()}: item id ({$id}) failed to delete",
                    [
                        'id' => $id,
                        'table' => $this->model->getTableName(),
                    ]
                )
            );
            return false;
        }
        return true;
    }

    public function deleteMany(array $data)
    {
        $results = $this->deleteBatchData($data);
        $this->cleanup();
        return $results;
    }

    private function singleWhereQueryData(array $data)
    {
        $query = '';
        foreach ($data as $index => $whereData) {
            if ($index > 0 && $index < count($data)) {
                $query .= " {$whereData['operator']} ";
            }
            switch ($whereData['compare']) {
                case Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_IN:
                    $query .= sprintf(
                        '(%s %s (%s))',
                        $whereData['column'],
                        $whereData['compare'],
                        implode(',', array_map(function ($value) use ($whereData) {
                                return $this->getDbPlaceholder($whereData);
                            }, $whereData['value'])
                        ));
                    foreach ($whereData['value'] as $value) {
                        $this->values[] = $value;
                    }
                    break;
                default:
                    $query .= sprintf(
                        '(%s %s %s)',
                        $whereData['column'],
                        $whereData['compare'],
                        $this->getDbPlaceholder($whereData)
                    );
                    if ($whereData['column'] === 'NULL') {
                        $this->values[] = 'NULL';
                    } else {
                        $this->values[] = $whereData['value'];
                    }
                    break;
            }
        }
        return $query;
    }

    private function buildWhereData()
    {
        if (!count($this->whereGroups)) {
            return $this->singleWhereQueryData($this->where);
        }
        $query = '';
        foreach ($this->whereGroups as $index => $whereGroup) {
            if ($index > 0 && $index < count($this->whereGroups)) {
                $query .= " {$whereGroup['operator']} ";
            }
            $query .= sprintf(
                '(%s)',
                $this->singleWhereQueryData($whereGroup['where'])
            );
        }
        return $query;
    }

    private function getDbPlaceholder(array $whereData)
    {
        if (!empty($whereData['model']) && $whereData['model'] instanceof Tru_Fetcher_DB_Model) {
            return $whereData['model']->getDbPlaceholderByColumn($whereData['column']);
        }
        return $this->model->getDbPlaceholderByColumn($whereData['column']);
    }

    protected function buildQuery()
    {
        $query = 'SELECT ';
        if (count($this->select)) {
            $query .= implode(', ', $this->select);
        } else {
            $query .= ' *';
        }
        $query .= " FROM {$this->model->getTableName($this->site, $this->isNetworkWide)}";
        if (count($this->joins)) {
            foreach ($this->joins as $join) {
                $query .= " {$join['type']} {$join['table']} ON {$join['on']}";
            }
        }
        if (count($this->where)) {
            $whereData = $this->buildWhereData();
            $query .= " WHERE {$whereData}";
        }
        if (count($this->groupBy)) {
            $query .= " GROUP BY " . implode(', ', $this->groupBy);
        }

        $orderBy = null;
        $orderDir = null;
        $modelOrderBy = $this->model->getOrderBy();
        $modelOrderDir = $this->model->getOrderDir();
        if (count($this->orderBy)) {
            $orderBy = implode(', ', $this->orderBy);
            if ($this->orderByDir) {
                $orderDir .= $this->orderByDir;
            }
        } elseif (!empty($modelOrderBy) && is_array($modelOrderBy) && count($modelOrderBy)) {
            $orderBy = implode(', ', $modelOrderBy);
            if (!empty($modelOrderDir) && is_string($modelOrderDir)) {
                $orderDir .= $modelOrderDir;
            }
        }

        if ($orderBy) {
            $query .= " ORDER BY {$orderBy}";
            if ($orderDir) {
                $query .= " {$orderDir}";
            }
        }
        if ($this->limit) {
            $query .= " LIMIT {$this->limit}";
        }
        if ($this->offset) {
            $query .= " OFFSET {$this->offset}";
        }
        return $query;
    }

    public function delete()
    {
        $delete = $this->db->query($this->buildDeleteQuery(), $this->values);
        $this->cleanup();
        return $delete;
    }

    public function buildDeleteQuery()
    {
        $query = "DELETE FROM {$this->model->getTableName($this->site, $this->isNetworkWide)}";
        if (count($this->where)) {
            $whereData = $this->buildWhereData();
            $query .= " WHERE {$whereData}";
        }
        return $query;
    }

    private function convertValueToType($value, $condition)
    {
        switch (
        (isset($condition[Tru_Fetcher_DB_Model_Constants::DATA_TYPE_KEY])) ? $condition[Tru_Fetcher_DB_Model_Constants::DATA_TYPE_KEY] : false
        ) {
            case Tru_Fetcher_DB_Model_Constants::DATA_TYPE_INT:
                return (int)$value;
            case Tru_Fetcher_DB_Model_Constants::DATA_TYPE_STRING:
            default:
                return (string)$value;
        }
    }

    private function getSaveDataWhereOperationValue(array $data, $key, $default)
    {
        return (isset($item[$key])) ? $item[$key] : $default;
    }

    private function buildSaveDataWhereConditionsArray(array $data)
    {
        $whereConditions = [];
        foreach ($this->getWhereQueryConditions() as $index => $condition) {
            if (!array_key_exists($condition['field'], $data)) {
                return new \WP_Error(
                    'tru_fetcher_db_save_error',
                    sprintf(
                        '%s not found in data',
                        $condition['field']
                    ),
                    $data
                );
            }
            $logicalOperator = $this->getSaveDataWhereOperationValue(
                $condition,
                Tru_Fetcher_DB_Model_Constants::LOGICAL_OPERATOR_KEY,
                Tru_Fetcher_DB_Model_Constants::DEFAULT_WHERE_LOGICAL_OPERATOR
            );
            $whereCompare = $this->getSaveDataWhereOperationValue(
                $condition,
                Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_KEY,
                Tru_Fetcher_DB_Model_Constants::DEFAULT_WHERE_COMPARE
            );
            $value = $data[$condition[Tru_Fetcher_DB_Model_Constants::FIELD_KEY]];
            $whereConditions[] = [
                Tru_Fetcher_DB_Model_Constants::LOGICAL_OPERATOR_KEY => $logicalOperator,
                Tru_Fetcher_DB_Model_Constants::FIELD_KEY => $condition[Tru_Fetcher_DB_Model_Constants::FIELD_KEY],
                Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_KEY => $whereCompare,
                Tru_Fetcher_DB_Model_Constants::VALUE_KEY => $this->db->getDbValue($this->convertValueToType($value,
                    $condition))
            ];
        }
        return $whereConditions;
    }

    private function buildSaveDataWhereConditionsStringArray(array $data)
    {
        $whereData = [
            'query' => [],
            'values' => [],
        ];

        $whereConditions = $this->buildSaveDataWhereConditionsArray($data);
        if (is_wp_error($whereConditions)) {
            return $whereConditions;
        }
        if (!count($whereConditions)) {
            return false;
        }
        foreach ($whereConditions as $index => $condition) {
            $logicalOperator = '';
            if ($index > 0) {
                $logicalOperator = "{$condition[Tru_Fetcher_DB_Model_Constants::LOGICAL_OPERATOR_KEY]} ";
            }
            $whereCompare = $condition[Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_KEY];
            $field = $condition[Tru_Fetcher_DB_Model_Constants::FIELD_KEY];
            $value = $condition[Tru_Fetcher_DB_Model_Constants::VALUE_KEY];

            if ($value === null) {
                $placeholder = 'NULL';
            } else {
                $placeholder = $this->model->getDbPlaceholderByColumn($field);
            }
            $whereData['query'][] = "{$logicalOperator}({$field} {$whereCompare} {$placeholder})";
            if ($value === null) {
                continue;
            }
            $whereData['values'][] = $value;
        }
        $whereData['query'] = implode(" ", $whereData['query']);
        return $whereData;
    }

    private function buildUpdateQueryData(array $data)
    {
        $updateSetData = [
            'set_query' => [],
            'values' => [],
            'placeholders' => [],
            'where_query' => '',
        ];

        foreach ($data as $column => $value) {
            if ($column === $this->model->getPrimaryKey()) {
                continue;
            }
            if ($value === null) {
                $updateSetData['set_query'][] = "{$column} = NULL";
                continue;
            } else {
                $updateSetData['set_query'][] = "{$column} = {$this->model->getDbPlaceholderByColumn($column)}";
            }
            $updateSetData['values'][] = $this->db->getDbValue($value);
        }

        $updateSetData['set_query'] = implode(", ", $updateSetData['set_query']);

        $whereConditionsArray = $this->buildSaveDataWhereConditionsStringArray($data);
        if (is_wp_error($whereConditionsArray)) {
            return $whereConditionsArray;
        }
        if (isset($whereConditionsArray['values'])) {
            $updateSetData['values'] = array_merge($updateSetData['values'], $whereConditionsArray['values']);
        }
        if (isset($whereConditionsArray['query'])) {
            $updateSetData['where_query'] = $whereConditionsArray['query'];
        }
        return $updateSetData;
    }

    private function buildSaveFieldValueData(array $item)
    {
        $data = [
            'fields' => [],
            'placeholders' => [],
            'values' => []
        ];
        foreach ($item as $name => $value) {
            $data['fields'][] = "$name";
            if ($value === null) {
                $data['placeholders'][] = 'NULL';
                continue;
            } else {
                $data['placeholders'][] = $this->model->getDbPlaceholderByColumn($name);
            }
            $data['values'][] = $this->db->getDbValue($value);
        }
        return $data;
    }

    private function buildItemInsertQueryArray(array $item)
    {
        $fieldValueData = $this->buildSaveFieldValueData($item);
        if (!isset($fieldValueData['fields']) || !is_array($fieldValueData['fields'])) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_fields_invalid',
                    'Fields is invalid'
                )
            );
            return false;
        }
        if (!isset($fieldValueData['values']) || !is_array($fieldValueData['values'])) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_values_invalid',
                    'Values is invalid'
                )
            );
            return false;
        }
        if (!isset($fieldValueData['placeholders']) || !is_array($fieldValueData['placeholders'])) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_placeholders_invalid',
                    'Placeholders is invalid'
                )
            );
            return false;
        }
        $fieldValueData['fields'] = implode(', ', $fieldValueData['fields']);
        $fieldValueData['placeholders'] = implode(', ', $fieldValueData['placeholders']);
        return $fieldValueData;
    }

    private function buildSaveDataWhereConditionsFetchArray(array $data)
    {
        $whereConditions = $this->buildSaveDataWhereConditionsArray($data);
        if (!count($whereConditions)) {
            return false;
        }
        $buildWhereStrings = [];
        foreach ($whereConditions as $index => $condition) {
            $buildWhereStrings[$condition[Tru_Fetcher_DB_Model_Constants::FIELD_KEY]] = $condition[Tru_Fetcher_DB_Model_Constants::VALUE_KEY];
        }
        return $buildWhereStrings;
    }

    public function insert(array $data)
    {
        global $wpdb;
        $validatedData = $this->getModel()->validateFields($data);
        if (!is_array($validatedData) || !count($validatedData)) {
            return false;
        }
        $validatedData = $this->getModel()->addDateQueryData($validatedData, $this->db::DB_OPERATION_INSERT);
        $insertQueryData = $this->buildItemInsertQueryArray($validatedData);

        $insertFields = $insertQueryData['fields'];
        $insertPlaceholders = $insertQueryData['placeholders'];
        $insertValues = $insertQueryData['values'];

        $query = "INSERT INTO {$this->getModel()->getTableName($this->site, $this->isNetworkWide)} 
                      ({$insertFields})
                      VALUES ({$insertPlaceholders});";

        $results = $this->db->runBatchQuery($query, $insertValues);
        if (is_wp_error($results)) {
            $this->addError($results);
            return false;
        }
        if (!$results) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_insert_error',
                    $wpdb->last_error,
                    $validatedData
                )
            );
            return false;
        }
        return $this->db->processBatchInsertResults($validatedData);
    }

    public function update(array $data)
    {
        global $wpdb;
        $validatedData = $this->getModel()->validateFields($data);
        $validatedData = $this->getModel()->addDateQueryData($validatedData, $this->db::DB_OPERATION_UPDATE);
        $updateSetData = $this->buildUpdateQueryData($validatedData);
        if (is_wp_error($updateSetData)) {
            $this->addError($updateSetData);
            return false;
        }
        $findWhereConditions = $this->buildSaveDataWhereConditionsFetchArray($validatedData);
        if (!$findWhereConditions) {
            return false;
        }
        $findDbRow = $this->db->find($this->model, $findWhereConditions);
        if (!$findDbRow) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_update_error',
                    'Update item not found',
                    $validatedData
                )
            );
            return false;
        }
        $query = "UPDATE {$this->getModel()->getTableName($this->site, $this->isNetworkWide)}
                  SET {$updateSetData['set_query']}
                  WHERE {$updateSetData['where_query']};";
        $results = $this->db->runBatchQuery($query, array_values($updateSetData['values']));

        if (is_wp_error($results)) {
            $this->addError($results);
            return false;
        }
        if ($results === false) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_insert_error',
                    $wpdb->last_error,
                    $validatedData
                )
            );
            return false;
        }

        return $this->db->findRow($this->model, $findWhereConditions);
    }


    public function deleteBatchData(array $data)
    {
        $errors = [];
        foreach ($data as $item) {
            if (!$this->deleteData($item)) {
                $errors[] = true;
            }
        }
        return count($errors) === 0;
    }

    public function deleteData(array $data)
    {
        global $wpdb;

        $whereConditionsArray = $this->buildSaveDataWhereConditionsStringArray($data);

        if (is_wp_error($whereConditionsArray)) {
            $this->addError($whereConditionsArray);
            return false;
        }
        $query = "DELETE FROM {$this->getModel()->getTableName($this->site, $this->isNetworkWide)}
                  WHERE {$whereConditionsArray['query']};";
        if ($this->db->runBatchQuery($query, $whereConditionsArray['values'], $this->db::DB_OPERATION_DELETE) ===
            false) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_delete_error',
                    $wpdb->last_error,
                    $data
                )
            );
            return false;
        } elseif ($wpdb->rows_affected === 0) {
            $this->addError(
                new \WP_Error(
                    'tru_fetcher_db_delete_error',
                    'No rows deleted',
                    $data
                )
            );
            return false;
        }
        return true;
    }

    /**
     * @return Tru_Fetcher_DB_Engine
     */
    public function getDb(): Tru_Fetcher_DB_Engine
    {
        return $this->db;
    }

    public function setSite(?\WP_Site $site): void
    {
        $this->db->setSite($site);
        $this->site = $site;
    }

    /**
     * @return Tru_Fetcher_DB_Model
     */
    public function getModel(): Tru_Fetcher_DB_Model
    {
        return $this->model;
    }

    /**
     * @param array $select
     */
    public function setSelect(array $select): self
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @param array $where
     */
    public function setWhere(array $where): self
    {
        $this->where = $where;
        return $this;
    }

    /**
     * @param array $orderBy
     */
    public function setOrderBy(array $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param string $orderByDir
     */
    public function setOrderByDir(string $orderByDir): self
    {
        $this->orderByDir = $orderByDir;
        return $this;
    }

    /**
     * @param array $groupBy
     */
    public function setGroupBy(array $groupBy): self
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @param int|null $limit
     */
    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @param int|null $offset
     */
    public function setOffset(?int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    public function addWhereGroup(array $whereData, ?string $op = 'AND'): self
    {
        $this->whereGroups[] = [
            'where' => $whereData,
            'operator' => $op
        ];
        return $this;
    }

    public function prepareWhereData(string $column, $value, string $compare = '=', string $operator = 'AND', ?Tru_Fetcher_DB_Model $model = null): array
    {
        return [
            'column' => $column,
            'value' => $value,
            'model' => $model,
            'compare' => $compare,
            'operator' => $operator,
        ];
    }

    public function addWhere(string $column, $value, string $compare = '=', string $operator = 'AND', ?Tru_Fetcher_DB_Model $model = null): self
    {
        $this->where[] = [
            'column' => $column,
            'value' => $value,
            'model' => $model,
            'compare' => $compare,
            'operator' => $operator,
        ];
        return $this;
    }

    public function addJoin(string $type, string $table, string $on): self
    {
        $this->joins[] = [
            'type' => $type,
            'table' => $table,
            'on' => $on,
        ];
        return $this;
    }

    /**
     * @return array
     */
    public function getWhereQueryConditions(): array
    {
        return $this->whereQueryConditions;
    }

    /**
     * @param array $whereQueryConditions
     */
    public function setWhereQueryConditions(array $whereQueryConditions): void
    {
        $this->whereQueryConditions = $whereQueryConditions;
    }

    /**
     * @return array
     */
    public function getJoins(): array
    {
        return $this->joins;
    }

    /**
     * @param array $joins
     */
    public function setJoins(array $joins): void
    {
        $this->joins = $joins;
    }

    protected function addWhereQueryCondition(string $field, string $compare = '=', string $dataType = Tru_Fetcher_DB_Model_Constants::DATA_TYPE_STRING): void
    {
        $this->whereQueryConditions[] = [
            Tru_Fetcher_DB_Model_Constants::FIELD_KEY => $field,
            Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_KEY => $compare,
            Tru_Fetcher_DB_Model_Constants::DATA_TYPE_KEY => $dataType,
        ];
    }
}
