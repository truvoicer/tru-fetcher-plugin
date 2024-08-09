<?php

namespace TruFetcher\Includes\DB\Model\Constants;

class Tru_Fetcher_DB_Model_Constants
{

	public const COLUMNS = 'columns';
	public const ALIAS = 'alias';
	public const FOREIGN_KEY_WP_REFERENCE = 'foreign_key_wp_reference';

	public const PIVOTS = 'pivots';
	public const PIVOT_TABLE = 'pivot_table';
	public const PIVOT_RELATIONS = 'pivot_relations';
	public const PIVOT_FOREIGN_TABLE = 'pivot_foreign_table';
	public const PIVOT_FOREIGN_KEY = 'pivot_foreign_key';
    public const PIVOT_FOREIGN_KEY_REFERENCE = 'pivot_foreign_key_reference';
    public const PIVOT_RELATED_TABLE = 'pivot_related_table';
    public const PIVOT_RELATED_KEY = 'pivot_related_key';
    public const PIVOT_RELATED_REF = 'pivot_related_ref';

	public const FOREIGN_KEY_CASCADE_DELETE = 'foreign_key_cascade_delete';
	public const FOREIGN_KEY_TARGET_COLUMN = 'foreign_key_target_column';
	public const FOREIGN_KEY_REFERENCE = 'foreign_key_reference';
	public const FOREIGN_KEY_REFERENCE_MODEL = 'foreign_key_reference_model';
	public const FOREIGN_KEY_REFERENCE_COLUMN = 'foreign_key_reference_column';
	public const FOREIGN_KEY_RELATION = 'foreign_key_relation';
	public const VALUE_KEY = 'value';
	public const ERROR_CODE_PREFIX = 'tru_fetcher';
	public const MODEL_KEY = 'model';

	public const MODEL_NAME_KEY = 'model_name';
	public const FIELD_KEY = 'field';
	public const DATA_TYPE_KEY = 'data_type';
	public const DATA_TYPE_INT = 'data_type_int';
	public const DATA_TYPE_STRING = 'data_type_string';
	public const WHERE_GROUP_CONDITION_KEY = 'where_group_condition';
	public const WHERE_GROUP_CONDITION_DEFAULT = 'OR';
	public const WHERE_COLUMN_CONDITION_KEY = 'where_column_condition';
	public const WHERE_COMPARE_KEY = 'where_compare';
    public const DEFAULT_WHERE_COMPARE = '=';
    public const WHERE_COMPARE_EQUALS = self::DEFAULT_WHERE_COMPARE;
	public const WHERE_COMPARE_IN = 'in';

	public const WHERE_COMPARE_NOT_IN = 'not in';
	public const LOGICAL_OPERATOR_KEY = 'logical_operator';
	public const DEFAULT_WHERE_LOGICAL_OPERATOR = 'AND';
	public const WHERE_LOGICAL_OPERATOR_AND = self::DEFAULT_WHERE_LOGICAL_OPERATOR;
	public const WHERE_LOGICAL_OPERATOR_OR = self::DEFAULT_WHERE_LOGICAL_OPERATOR;
	public const CONDITIONS_KEY = 'conditions';
	public const DATA_KEY = 'data';
	public const FOREIGN_KEYS_FIELD = 'foreign_keys';
	public const PRIMARY_KEY_FIELD = 'primary_key';
	public const UNIQUE_CONSTRAINT_FIELD = 'unique_constraint';

	public const SORT_ORDER_ASC = 'asc';
	public const SORT_ORDER_DESC = 'desc';
}
