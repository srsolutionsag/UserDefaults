<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ActiveRecord;
use ActiveRecordList;
use ilObjUser;
use ilUserDefaultsPlugin;
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
	 * @var UDFCheck[]
	 */
	public static $class_names = [
		UDFCheckUser::FIELD_CATEGORY => UDFCheckUser::class,
		UDFCheckUDF::FIELD_CATEGORY => UDFCheckUDF::class
	];


	/**
	 * @return string
	 */
	public final function getConnectorContainerName() {
		return static::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public final static function returnDbTableName() {
		return static::TABLE_NAME;
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
	 * @param int   $parent_id
	 * @param bool  $array
	 * @param array $filter
	 * @param array $limit
	 *
	 * @return array
	 */
	public static function getChecksByParent($parent_id, $array = false, array $filter = [], array $limit = []) {
		$checks = [];

		$where_array = [ 'parent_id' => $parent_id ];
		foreach ($filter as $field => $value) {
			if (!empty($value)) {
				$where_array[$field] = $value;
			}
		}

		foreach (self::$class_names as $class) {
			/**
			 * @var ActiveRecordList $where
			 */
			$where = $class::where($where_array);

			if (count($limit) === 2) {
				$where = $where->limit($limit[0], $limit[1]);
			}

			$checks = array_merge($checks, $where->get());
		}

		if ($array) {
			$checks = array_map(function (UDFCheck $check) {
				$check_array = get_object_vars($check);

				$check_array["field_category"] = $check->getFieldCategory();
				$check_array["field_key_txt"] = $check->getDefinition()["txt"];

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
	 * @return array
	 */
	public static function getDefinitions() {
		if (self::$all_definitions !== NULL) {
			return self::$all_definitions;
		}

		self::$all_definitions = [];

		foreach (self::$class_names as $class) {
			self::$all_definitions = array_merge(self::$all_definitions, $class::getDefinitionsOfCategory());
		}

		return self::$all_definitions;
	}


	/**
	 * @return array
	 */
	public static function getDefinitionsOfCategoryOptions() {
		$return = [];

		foreach (static::getDefinitionsOfCategory() as $definition) {
			$return[$definition['field_key']] = $definition['txt'];
		}

		return $return;
	}


	/**
	 * @param string $field_key
	 *
	 * @return int
	 */
	public static function getCategoryForFieldKey($field_key) {
		foreach (self::getDefinitions() as $definition) {
			if ($definition['field_key'] == $field_key) {
				return $definition['field_category'];
			}
		}

		return 0;
	}


	/**
	 * @return array
	 */
	public function getDefinition() {
		foreach (static::getDefinitionsOfCategory() as $definition) {
			if ($definition['field_key'] == $this->field_key) {
				return $definition;
			}
		}

		return [];
	}


	/**
	 * @return array
	 */
	public function getDefinitionValues() {
		$definition = $this->getDefinition();

		$return = [];

		foreach ($definition['field_values'] as $val) {
			$return[$val] = $val;
		}

		return $return;
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
	 * @return int
	 */
	public final function getFieldCategory() {
		return static::FIELD_CATEGORY;
	}


	/**
	 * @var string
	 *
	 * @abstract
	 */
	const TABLE_NAME = '';
	/**
	 * @var int
	 *
	 * @abstract
	 */
	const FIELD_CATEGORY = '';


	/**
	 * @return array
	 */
	protected static abstract function getDefinitionsOfCategory();


	/**
	 * @return array
	 */
	protected abstract function getFieldValue(ilObjUser $user);
}
