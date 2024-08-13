<?php

namespace TruFetcher\Includes\DB\Repository\Relations;

use TruFetcher\Includes\DB\Model\Constants\Tru_Fetcher_DB_Model_Constants;
use TruFetcher\Includes\DB\Model\Tru_Fetcher_DB_Model;
use TruFetcher\Includes\Tru_Fetcher_Helpers;

class Tru_Fetcher_DB_Repository_Relations_Sync extends Tru_Fetcher_DB_Repository_Relations_Base
{
    public const SYNC_MODE_TOGGLE = 'toggle';
    public const SYNC_MODE_REPLACE = 'replace';

    public const SYNC_MODES = [
        self::SYNC_MODE_TOGGLE,
        self::SYNC_MODE_REPLACE
    ];

    private string $syncMode;
    private array $conditions = [];

    public function setSyncMode(string $syncMode): self
    {
        $this->syncMode = $syncMode;
        return $this;
    }

    public function setSyncModeToggle(): self
    {
        $this->syncMode = self::SYNC_MODE_TOGGLE;
        return $this;
    }

    public function setSyncModeReplace(): self
    {
        $this->syncMode = self::SYNC_MODE_REPLACE;
        return $this;
    }

    public function setConditions(array $conditions): self
    {
        $this->conditions = $conditions;
        return $this;
    }

    private function doesItemMatchPivotRelation(Tru_Fetcher_DB_Model $model, array $pivotRelations, array $item, ?bool $pivotSourcedData = false)
    {
        $item = [
            ...$item,
            ...$this->buildConditionKeyValueArray()
        ];
        foreach ($pivotRelations as $relation) {
            $pivotForeignTable = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_TABLE];
            $pivotForeignKey = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY];
            $pivotForeignKeyReference = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY_REFERENCE];

            if (!$pivotSourcedData) {
                if ($model::class === $pivotForeignTable) {
                    $keyCond = $pivotForeignKeyReference;
                } else {
                    $keyCond = $pivotForeignKey;
                }
            } else {
                $keyCond = $pivotForeignKey;
            }
            if (!isset($item[$keyCond])) {
                return false;
            }
        }
        return true;
    }

    private function getItemPivotRelationData(Tru_Fetcher_DB_Model $model, array $relation, array $item, ?bool $pivotSourcedData = false)
    {
        $pivotForeignTable = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_TABLE];
        $pivotForeignKey = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY];
        $pivotForeignKeyReference = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY_REFERENCE];

        if (!$pivotSourcedData) {
            if ($model::class === $pivotForeignTable) {
                $value = (isset($item[$pivotForeignKeyReference])) ? $item[$pivotForeignKeyReference] : null;
            } else {
                $value = (isset($item[$pivotForeignKey])) ? $item[$pivotForeignKey] : null;
            }
        } else {
            $value = (isset($item[$pivotForeignKey])) ? $item[$pivotForeignKey] : null;
        }
        return [
            'key' => $pivotForeignKey,
            'value' => $value
        ];
    }

    private function setExistingPivotDataWhereQueryForReplace(
        Tru_Fetcher_DB_Model $model,
        array                $pivotRelations,
        array                $data,
        ?bool                $pivotSourcedData = false
    )
    {
        foreach ($data as $item) {
            $whereData = [];
            if (!$this->doesItemMatchPivotRelation($model, $pivotRelations, $item, $pivotSourcedData)) {
                continue;
            }
            foreach ($pivotRelations as $relation) {
                $itemPivotRelData = $this->getItemPivotRelationData($model, $relation, $item, $pivotSourcedData);
                if (in_array($itemPivotRelData['key'], array_column($this->conditions, Tru_Fetcher_DB_Model_Constants::COLUMN))) {
                    continue;
                }
                $whereData[] = $this->repoBase->prepareWheredata(
                    $itemPivotRelData['key'],
                    $itemPivotRelData['value'],
                    Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_NOT_EQUALS
                );
            }
            $this->repoBase->addWhereGroup($whereData);
        }
        $conditionsWhereData = [];
        foreach ($this->conditions as $condition) {
            $conditionsWhereData[] = $this->repoBase->prepareWheredata(
                $condition[Tru_Fetcher_DB_Model_Constants::COLUMN],
                $condition[Tru_Fetcher_DB_Model_Constants::VALUE],
                $condition[Tru_Fetcher_DB_Model_Constants::COMPARE]
            );
        }

        $this->repoBase->addWhereGroup($conditionsWhereData);
    }

    private function setExistingPivotDataDeleteQueryForReplace(
        Tru_Fetcher_DB_Model $model,
        array                $pivotRelations,
        array                $data,
        ?bool                $pivotSourcedData = false
    )
    {
        foreach ($data as $item) {
            $whereData = [];
            if (!$this->doesItemMatchPivotRelation($model, $pivotRelations, $item, $pivotSourcedData)) {
                continue;
            }
            foreach ($pivotRelations as $relation) {
                $itemPivotRelData = $this->getItemPivotRelationData($model, $relation, $item, $pivotSourcedData);
                if (in_array($itemPivotRelData['key'], array_column($this->conditions, Tru_Fetcher_DB_Model_Constants::COLUMN))) {
                    continue;
                }
                $whereData[] = $this->repoBase->prepareWheredata(
                    $itemPivotRelData['key'],
                    $itemPivotRelData['value'],
                    Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_EQUALS,
                    Tru_Fetcher_DB_Model_Constants::WHERE_LOGICAL_OPERATOR_OR
                );
            }
            $this->repoBase->addWhereGroup($whereData,Tru_Fetcher_DB_Model_Constants::WHERE_LOGICAL_OPERATOR_OR);
        }
        $conditionsWhereData = [];
        foreach ($this->conditions as $condition) {
            $conditionsWhereData[] = $this->repoBase->prepareWheredata(
                $condition[Tru_Fetcher_DB_Model_Constants::COLUMN],
                $condition[Tru_Fetcher_DB_Model_Constants::VALUE],
                $condition[Tru_Fetcher_DB_Model_Constants::COMPARE]
            );
        }

        $this->repoBase->addWhereGroup($conditionsWhereData);
    }

    private function setExistingPivotDataWhereQueryForToggle(
        Tru_Fetcher_DB_Model $model,
        array                $pivotRelations,
        array                $data,
        ?bool                $pivotSourcedData = false
    )
    {
        foreach ($data as $item) {
            $whereData = [];
            if (!$this->doesItemMatchPivotRelation($model, $pivotRelations, $item, $pivotSourcedData)) {
                continue;
            }
            foreach ($pivotRelations as $relation) {
                $itemPivotRelData = $this->getItemPivotRelationData($model, $relation, $item, $pivotSourcedData);
                $whereData[] = $this->repoBase->prepareWheredata(
                    $itemPivotRelData['key'],
                    $itemPivotRelData['value'],
                    Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_EQUALS
                );
            }
            foreach ($this->conditions as $condition) {
                $whereData[] = $this->repoBase->prepareWheredata(
                    $condition[Tru_Fetcher_DB_Model_Constants::COLUMN],
                    $condition[Tru_Fetcher_DB_Model_Constants::VALUE],
                    $condition[Tru_Fetcher_DB_Model_Constants::COMPARE]
                );
            }

            $this->repoBase->addWhereGroup($whereData, 'OR');
        }
    }

    private function buildConditionKeyValueArray()
    {
        return array_combine(
            array_column($this->conditions, Tru_Fetcher_DB_Model_Constants::COLUMN),
            array_column($this->conditions, Tru_Fetcher_DB_Model_Constants::VALUE)
        );
    }

    private function removeExistingPivotItems(array $results, array $pivotRelations, array $data)
    {
        return array_filter($data, function ($item) use ($results, $pivotRelations) {
            $item = [
                ...$item,
                ...$this->buildConditionKeyValueArray()
            ];
            if (!$this->doesItemMatchPivotRelation($this->repoBase->getModel(), $pivotRelations, $item)) {
                return true;
            }
            $whereConditions = [];
            foreach ($pivotRelations as $relation) {
                $pivotForeignTable = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_TABLE];
                $pivotForeignKey = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY];
                $pivotForeignKeyReference = $relation[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY_REFERENCE];

                if ($this->repoBase->getModel()::class === $pivotForeignTable) {
                    $column = $pivotForeignKeyReference;
                    $value = $item[$pivotForeignKeyReference];
                } else {
                    $value = $item[$pivotForeignKey];
                    $column = $pivotForeignKey;
                }

                $whereConditions[$column] = $value;
            }

            return Tru_Fetcher_Helpers::findInArray(
                    $whereConditions,
                    array_map(fn($result) => [...$result, ...$this->buildConditionKeyValueArray()], $results),
                    true
                ) === false;
        }, ARRAY_FILTER_USE_BOTH);
    }

    public function sync(Tru_Fetcher_DB_Model $pivotModel, array $data)
    {
        if (!isset($this->syncMode) || !in_array($this->syncMode, self::SYNC_MODES)) {
            $this->repoBase->addError(
                new \WP_Error(
                    'tru_fetcher_db_sync_error',
                    'Invalid sync mode',
                    $this->syncMode ?? 'sync mode not set'
                )
            );
            return false;
        }
        $pivotConfig = $this->repoBase->getModel()->getPivotConfigByModel($pivotModel);
        if (!$pivotConfig) {
            return false;
        }

        $pivotRelations = $pivotConfig[Tru_Fetcher_DB_Model_Constants::PIVOT_RELATIONS];

        $instance = $this->repoBase::getInstance($pivotModel)
            ->getRelations()
            ->getSync()
            ->setSyncMode($this->syncMode)
            ->setConditions($this->conditions);

        switch ($this->syncMode) {
            case self::SYNC_MODE_TOGGLE:
                $instance->setExistingPivotDataWhereQueryForToggle(
                    $this->repoBase->getModel(),
                    $pivotRelations,
                    $data
                );
                $results = $instance->repoBase->findMany();
                if (count($results)) {
                    $instance->setExistingPivotDataWhereQueryForToggle(
                        $this->repoBase->getModel(),
                        $pivotRelations,
                        $results,
                        true
                    );
                    $instance->repoBase->delete();
                    $filteredData = $this->removeExistingPivotItems($results, $pivotRelations, $data);
                }
                break;
            case self::SYNC_MODE_REPLACE:
                $instance->setExistingPivotDataWhereQueryForReplace(
                    $this->repoBase->getModel(),
                    $pivotRelations,
                    $data
                );
                $results = $instance->repoBase->findMany();
                $filteredData = $data;
                if (count($results)) {
                    $instance->setExistingPivotDataDeleteQueryForReplace(
                        $this->repoBase->getModel(),
                        $pivotRelations,
                        $results,
                        true
                    );
                    $instance->repoBase->delete();
                    $filteredData = $this->removeExistingPivotItems($results, $pivotRelations, $data);
                }
                break;
        }

        $thisPivotConfig = $this->repoBase->getModel()->findPivotForeignKeyConfigByModel($pivotModel, $this->repoBase->getModel());
        if (!$thisPivotConfig) {
            return false;
        }
        $thisPivotForeignKeyRef = $thisPivotConfig[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY_REFERENCE];
        $thisPivotForeignKey = $thisPivotConfig[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY];

        $whereValues = array_column($filteredData, $thisPivotForeignKeyRef);
        if (count($whereValues)) {
            $this->repoBase->setSelect([
                "{$this->repoBase->getModel()->getTableName()}.*",
                "{$pivotModel->getTableName()}.*",
                "{$this->repoBase->getModel()->getTableName()}.{$thisPivotForeignKeyRef} as {$thisPivotForeignKeyRef}",
            ]);
            $this->repoBase->addJoin(
                'left join',
                $pivotModel->getTableName(),
                "{$this->repoBase->getModel()->getTableName()}.{$thisPivotForeignKeyRef} = {$pivotModel->getTableName()}.{$thisPivotForeignKey}"
            );
            $this->repoBase->addWhere(
                "{$this->repoBase->getModel()->getTableName()}.{$thisPivotForeignKeyRef}",
                $whereValues,
                Tru_Fetcher_DB_Model_Constants::WHERE_COMPARE_IN
            );
            $findSkills = $this->repoBase->findMany();
            foreach ($findSkills as $findSkill) {
                if (!empty($findSkill[$thisPivotForeignKey])) {
                    continue;
                }
                $insertIntoPivot = $this->insertPivotTableItem($pivotModel, array_merge($findSkill, $this->buildConditionKeyValueArray()));
                if (!$insertIntoPivot) {
                    $this->repoBase->addError(
                        new \WP_Error(
                            'tru_fetcher_db_sync_error',
                            'Failed to insert into pivot table',
                            $findSkill
                        )
                    );
                    return false;
                }
            }
            $filteredData = array_filter($filteredData, function ($item) use ($findSkills, $thisPivotForeignKeyRef) {
                return Tru_Fetcher_Helpers::findInArray(
                        [$thisPivotForeignKeyRef => $item[$thisPivotForeignKeyRef]],
                        $findSkills,
                        true
                    ) === false;
            });
            $this->syncInsert($pivotModel, $filteredData);
        } else {
            $this->syncInsert($pivotModel, $filteredData);
        }

        return $this->repoBase->hasErrors();

    }

    private function syncInsert(Tru_Fetcher_DB_Model $pivotModel, array $data)
    {
        $columns = $this->repoBase->getModel()->getTableColumns();
        foreach ($data as $item) {
            $item = [
                ...$item,
                ...$this->buildConditionKeyValueArray()
            ];
            $insertData = $this->buildSyncInsertDataItem($columns, $item);
            $validate = $this->repoBase->getModel()->validateFields($insertData, $this->repoBase->getModel()->getRequiredFields(), false);
            if (is_wp_error($validate)) {
                $this->repoBase->addError($validate);
                return false;
            }

            $insert = $this->repoBase->insert($insertData);
            if (!$insert) {
                $this->repoBase->addError(
                    new \WP_Error(
                        'tru_fetcher_db_sync_error',
                        'Failed to insert into table',
                        $insertData
                    )
                );
                return false;
            }
            $insertIntoPivot = $this->insertPivotTableItem($pivotModel, array_merge($item, $insert));
            if (!$insertIntoPivot) {
                $this->repoBase->addError(
                    new \WP_Error(
                        'tru_fetcher_db_sync_error',
                        'Failed to insert into pivot table',
                        $item
                    )
                );
                return false;
            }
        }
    }

    private function insertPivotTableItem(Tru_Fetcher_DB_Model $pivotModel, array $item)
    {
        $thisPivotConfig = $this->repoBase->getModel()->findPivotForeignKeyConfigByModel($pivotModel, $this->repoBase->getModel());
        if (!$thisPivotConfig) {
            return false;
        }
        $thisPivotForeignKeyRef = $thisPivotConfig[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY_REFERENCE];
        $thisPivotForeignKey = $thisPivotConfig[Tru_Fetcher_DB_Model_Constants::PIVOT_FOREIGN_KEY];

        $instance = $this->repoBase::getInstance($pivotModel);
        $pivotInsertData = $this->buildSyncInsertDataItem($pivotModel->getTableColumns(), $item);
        $pivotInsertData[$thisPivotForeignKey] = $item[$thisPivotForeignKeyRef];
        unset($pivotInsertData[$thisPivotForeignKeyRef]);

        $insertPivot = $instance->insert($pivotInsertData);
        if (!$insertPivot) {
            return false;
        }
        return $insertPivot;
    }

    private function buildSyncInsertDataItem(array $columns, array $data)
    {
        return array_intersect_key($data, array_flip($columns));
    }

}
