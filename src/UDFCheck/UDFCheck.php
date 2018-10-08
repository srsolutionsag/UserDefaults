<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ActiveRecord;
use ActiveRecordList;
use ilObjUser;
use ilUserDefaultsPlugin;
use ilUserDefinedFields;
use ilUserSearchOptions;
use srag\ActiveRecordConfig\ActiveRecordConfig;
use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class UDFCheck
 *
 * @package srag\Plugins\UserDefaults\UDFCheck
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
abstract class UDFCheck extends ActiveRecord {

	use DICTrait;
	use UserDefaultsTrait;
	/**
	 * @var string
	 *
	 * @abstract
	 */
	const TABLE_NAME = '';
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const OP_EQUALS = 1;
	const OP_STARTS_WITH = 2;
	const OP_CONTAINS = 3;
	const OP_ENDS_WITH = 4;
	const OP_NOT_EQUALS = 5;
	const OP_NOT_STARTS_WITH = 6;
	const OP_NOT_CONTAINS = 7;
	const OP_NOT_ENDS_WITH = 8;
	const OP_IS_EMPTY = 9;
	const OP_NOT_IS_EMPTY = 10;
	const OP_REG_EX = 11;
	const STATUS_INACTIVE = 1;
	const STATUS_ACTIVE = 2;
	const CHECK_SPLIT = ' → ';
	const FIELD_CATEGORY_USR = 1;
	const FIELD_CATEGORY_UDF = 2;
	/**
	 * @var array|null
	 */
	protected static $all_definitions = NULL;
	/**
	 * @var array
	 */
	public static $operator_text_keys = array(
		self::OP_EQUALS => 'equals',
		self::OP_STARTS_WITH => 'starts_with',
		self::OP_CONTAINS => 'contains',
		self::OP_ENDS_WITH => 'ends_with',
		self::OP_NOT_EQUALS => 'not_equals',
		self::OP_NOT_STARTS_WITH => 'not_starts_with',
		self::OP_NOT_CONTAINS => 'not_contains',
		self::OP_NOT_ENDS_WITH => 'not_ends_with',
		self::OP_IS_EMPTY => 'is_empty',
		self::OP_NOT_IS_EMPTY => 'not_is_empty',
		self::OP_REG_EX => 'reg_ex',
	);
	/**
	 * @var array
	 */
	protected static $class_names = [
		self::FIELD_CATEGORY_USR => UDFCheckUser::class,
		self::FIELD_CATEGORY_UDF => UDFCheckUDF::class
	];


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return static::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return static::TABLE_NAME;
	}


	/**
	 * @param int   $parent_id
	 * @param bool  $array
	 * @param array $filter
	 * @param array $limit
	 *
	 * @return array
	 */
	public static function getChecksByParent($parent_id, $array = false, array $filter = [], array $limit = []) {
		$checks = [];

		foreach (self::$class_names as $class) {
			$where = [ 'parent_id' => $parent_id ];

			foreach ($filter as $field => $value) {
				if (!empty($value)) {
					$where[$field] = $value;
				}
			}

			/**
			 * @var ActiveRecordList $where
			 */
			$where = $class::where($where);

			if (count($limit) === 2) {
				$where = $where->limit($limit[0], $limit[1]);
			}

			$checks = array_merge($checks, $where->get());
		}

		if ($array) {
			$checks = array_map(function (UDFCheck $check) {
				$check_array = get_object_vars($check);

				$check_array["field_category"] = $check->getFieldCategory();

				return $check_array;
			}, $checks);
		}

		return $checks;
	}


	/**
	 * @param int $parent_id
	 *
	 * @return bool
	 */
	public static function hasChecks($parent_id) {
		foreach (self::$class_names as $class) {
			if ($class::where([ 'parent_id' => $parent_id ])->hasSets()) {
				return true;
			}
		}

		return false;
	}


	/**
	 * @param int $field_category
	 * @param int $id
	 *
	 * @return static|null
	 */
	public static function getCheckById($field_category, $id) {
		$class = self::$class_names[$field_category];

		if ($class !== NULL) {
			$check = $class::where([ 'id' => $id ])->first();
		} else {
			$check = NULL;
		}

		return $check;
	}


	/**
	 * @param int $field_category
	 *
	 * @return static
	 */
	public static function newInstance($field_category) {
		$class = self::$class_names[$field_category];

		if ($class !== NULL) {
			$check = new $class();
		} else {
			$check = new UDFCheckUDF();
		}

		return $check;
	}


	/**
	 * @var int
	 *
	 * @con_is_primary true
	 * @con_is_unique  true
	 * @con_sequence   true
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $parent_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 *
	 */
	protected $field_key = 1;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected $check_value = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected $operator = self::OP_EQUALS;
	/**
	 * @var bool
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected $negated = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $owner = 6;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected $status = self::STATUS_ACTIVE;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected $create_date;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected $update_date;


	/**
	 * @return int
	 */
	public abstract function getFieldCategory();


	/**
	 * @param int   $primary_key
	 * @param array $add_constructor_args
	 *
	 * @return static
	 */
	public static function find($primary_key, array $add_constructor_args = array()) {
		return parent::find($primary_key, $add_constructor_args);
	}


	/**
	 *
	 */
	public function update() {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		parent::update();
	}


	/**
	 *
	 */
	public function create() {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		$this->setCreateDate(time());
		parent::create();
	}


	/**
	 * @param string $check_value
	 */
	public function setCheckValue($check_value) {
		$this->check_value = $check_value;
	}


	/**
	 * @param string[] $check_values
	 */
	public function setCheckValues(array $check_values) {
		$this->check_value = implode(self::CHECK_SPLIT, array_map(function ($check_value) {
			return trim($check_value);
		}, $check_values));
	}


	/**
	 * @return string
	 */
	public function getCheckValue() {
		return $this->check_value;
	}


	/**
	 * @return string[]
	 */
	public function getCheckValues() {
		return array_map(function ($check_value) {
			return trim($check_value);
		}, explode(self::CHECK_SPLIT, $this->check_value));
	}


	/**
	 * @param string $field_key
	 */
	public function setFieldKey($field_key) {
		$this->field_key = $field_key;
	}


	/**
	 * @return string
	 */
	public function getFieldKey() {
		return $this->field_key;
	}


	/**
	 * @param int $operator
	 */
	public function setOperator($operator) {
		$this->operator = $operator;
	}


	/**
	 * @return int
	 */
	public function getOperator() {
		return $this->operator;
	}


	/**
	 * @param int $create_date
	 */
	public function setCreateDate($create_date) {
		$this->create_date = $create_date;
	}


	/**
	 * @return int
	 */
	public function getCreateDate() {
		return $this->create_date;
	}


	/**
	 * @param int $id
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}


	/**
	 * @return int
	 */
	public function getOwner() {
		return $this->owner;
	}


	/**
	 * @param int $update_date
	 */
	public function setUpdateDate($update_date) {
		$this->update_date = $update_date;
	}


	/**
	 * @return int
	 */
	public function getUpdateDate() {
		return $this->update_date;
	}


	/**
	 * @param int $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}


	/**
	 * @return int
	 */
	public function getStatus() {
		return $this->status;
	}


	/**
	 * @param int $parent_id
	 */
	public function setParentId($parent_id) {
		$this->parent_id = $parent_id;
	}


	/**
	 * @return int
	 */
	public function getParentId() {
		return $this->parent_id;
	}


	/**
	 * @return boolean
	 */
	public function isNegated() {
		return $this->negated;
	}


	/**
	 * @param boolean $negated
	 */
	public function setNegated($negated) {
		$this->negated = $negated;
	}


	/**
	 * @param $field_name
	 *
	 * @return mixed|null|string
	 */
	public function sleep($field_name) {
		switch ($field_name) {
			case 'create_date':
			case 'update_date':
				return date(ActiveRecordConfig::SQL_DATE_FORMAT, $this->{$field_name});
				break;
		}

		return NULL;
	}


	/**
	 * @param $field_name
	 * @param $field_value
	 *
	 * @return mixed|null
	 */
	public function wakeUp($field_name, $field_value) {
		switch ($field_name) {
			case 'create_date':
			case 'update_date':
				return strtotime($field_value);
				break;
		}

		return NULL;
	}


	/**
	 * @return array
	 */
	protected abstract function getFieldValue(ilObjUser $user);


	/**
	 * @param ilObjUser $user
	 *
	 * @return bool
	 */
	public function isValid(ilObjUser $user) {
		$values = array_map(function ($value) {
			return trim($value);
		}, $this->getFieldValue($user));

		$check_values = $this->getCheckValues();

		foreach ($check_values as $key => $check_value) {
			$value = reset($values); //TODO: ???

			if (!empty($value) && !empty($check_value)) {
				switch ($this->getOperator()) {
					case self::OP_EQUALS:
						$valid = ($value === $check_value);
						break;

					case self::OP_NOT_EQUALS:
						$valid = ($value !== $check_value);
						break;

					case self::OP_STARTS_WITH:
						$valid = (strpos($value, $check_value) === 0);
						break;

					case self::OP_NOT_STARTS_WITH:
						$valid = (strpos($value, $check_value) !== 0);
						break;

					case self::OP_ENDS_WITH:
						$valid = (strrpos($value, $check_value) === (strlen($value) - strlen($check_value)));
						break;

					case self::OP_NOT_ENDS_WITH:
						$valid = (strrpos($value, $check_value) !== (strlen($value) - strlen($check_value)));
						break;

					case self::OP_CONTAINS:
						$valid = (strpos($value, $check_value) !== false);
						break;

					case self::OP_NOT_CONTAINS:
						$valid = (strpos($value, $check_value) === false);
						break;

					case self::OP_IS_EMPTY:
						$valid = empty($value);
						break;

					case self::OP_NOT_IS_EMPTY:
						$valid = (!empty($value));
						break;

					case self::OP_REG_EX:
						// Fix RegExp
						if ($check_value[0] !== "/" && $check_value[strlen($check_value) - 1] !== "/") {
							$check_value = "/$check_value/";
						}
						$valid = (preg_match($check_value, $value) === 1);
						break;

					default:
						return false;
				}
			}

			if (!$valid) {
				break;
			}
		}

		$b = (!$this->isNegated() === $valid);

		return $b;
	}


	/**
	 * @return array
	 */
	public static function getAllDefinitions() {
		if (!is_null(self::$all_definitions)) {
			return self::$all_definitions;
		}

		$usr_fields = array();
		foreach (ilUserSearchOptions::_getSearchableFieldsInfo(true) as $field) {
			$usr_field = array();

			if (!in_array($field['type'], array( FIELD_TYPE_TEXT, FIELD_TYPE_SELECT, FIELD_TYPE_MULTI ))) {
				continue;
			}

			$usr_field["txt"] = $field["lang"];
			$usr_field["field_category"] = self::FIELD_CATEGORY_USR;
			$usr_field["field_key"] = $field["db"];
			$usr_field["field_type"] = $field["type"];
			$usr_field["field_values"] = $field["values"];

			$usr_fields[] = $usr_field;
		}

		$udf_fields = array();
		$user_defined_fields = ilUserDefinedFields::_getInstance();
		foreach ($user_defined_fields->getDefinitions() as $field) {
			$udf_field = array();

			if (!in_array($field['field_type'], array( UDF_TYPE_TEXT, UDF_TYPE_SELECT ))) {
				continue;
			}

			$udf_field["txt"] = $field["field_name"];
			$udf_field["field_category"] = self::FIELD_CATEGORY_UDF;
			$udf_field["field_key"] = $field["field_id"];
			$udf_field["field_type"] = $field["field_type"];
			$udf_field["field_values"] = $field["field_values"];

			$udf_fields[] = $udf_field;
		}

		self::$all_definitions = array_merge($usr_fields, $udf_fields);

		return self::$all_definitions;
	}


	/**
	 * @param int $id
	 *
	 * @return array
	 */
	public static function getDefinitionForId($id) {
		$definitions = self::getAllDefinitions();

		return $definitions[$id];
	}


	/**
	 * @return array
	 */
	public static function getDefinitionData() {
		/*return array_map(function (array $field) {
			return $field["txt"];
		}, self::getAllDefinitions());*/
		$return = array();
		foreach (self::getAllDefinitions() as $def) {
			$return[$def['field_key']] = $def['txt'];
		}

		return $return;
	}


	/**
	 * @param string $field_keyd
	 *
	 * @return array
	 */
	public static function getDefinitionValuesForKey($field_keyd) {
		$return = array();

		foreach (self::getAllDefinitions() as $def) {
			if ($def['field_key'] == $field_keyd) {
				foreach ($def['field_values'] as $val) {
					$return[$val] = $val;
				}

				return $return;
			}
		}

		return array();
	}


	/**
	 * @param string $field_key
	 *
	 * @return int
	 */
	public static function getDefinitionTypeForKey($field_key) {
		foreach (self::getAllDefinitions() as $def) {
			if ($def['field_key'] == $field_key) {
				return $def['field_type'];
			}
		}

		return 0;
	}


	/**
	 * @param string $field_key
	 *
	 * @return int
	 */
	public static function getDefinitionCategoryForKey($field_key) {
		foreach (self::getAllDefinitions() as $def) {
			if ($def['field_key'] == $field_key) {
				return $def['field_category'];
			}
		}

		return 0;
	}


	/**
	 * @param string $field_key
	 * @param string $field_category
	 *
	 * @return int
	 */
	public static function getDefinitionFieldTitleForKey($field_key) {
		foreach (self::getAllDefinitions() as $def) {
			if ($def['field_key'] == $field_key) {
				return $def['txt'];
			}
		}

		return 0;
	}
}
