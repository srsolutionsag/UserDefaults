<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ActiveRecord;
use ActiveRecordList;
use ilObjUser;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Config\UserDefaultsConfig;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;


abstract class UDFCheck extends ActiveRecord {

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
	protected static ?array $all_definitions = NULL;
	/**
	 * @var array
	 */
	public static array $operator_text_keys = array(
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

	public static array $operator_positive = [
		self::OP_EQUALS,
		self::OP_STARTS_WITH,
		self::OP_CONTAINS,
		self::OP_ENDS_WITH,
		self::OP_IS_EMPTY,
		self:: OP_REG_EX,
	];

	public static array $operator_negative = [
		self::OP_NOT_EQUALS,
		self::OP_NOT_STARTS_WITH,
		self::OP_NOT_CONTAINS,
		self::OP_NOT_ENDS_WITH,
		self::OP_NOT_IS_EMPTY,
	];

	public static array $class_names = [
		UDFCheckUser::FIELD_CATEGORY => UDFCheckUser::class,
		UDFCheckUDF::FIELD_CATEGORY => UDFCheckUDF::class
	];
    private ilObjUser $user;

    public final function getConnectorContainerName(): string
    {
		return static::TABLE_NAME;
	}

	public final static function returnDbTableName(): string
    {
		return static::TABLE_NAME;
	}

    public function __construct($primary_key = 0)
    {
        global $DIC;
        parent::__construct($primary_key);

        $this->user = $DIC->user();
    }


    public static function newInstance(int $field_category): UDFCheckUDF|static
    {
		$class = self::$class_names[$field_category];

		if ($class !== NULL) {
			$check = new $class();
		} else {
			$check = new UDFCheckUDF();
		}

		return $check;
	}


	public static function getChecksByParent(int $parent_id, bool $array = false, array $filter = [], array $limit = []): array
    {
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

	public static function hasChecks(int $parent_id): bool
    {
		foreach (self::$class_names as $class) {
			if ($class::where([ 'parent_id' => $parent_id ])->hasSets()) {
				return true;
			}
		}

		return false;
	}


	public static function getCheckById(int $field_category, int $id): ?static
    {
		$class = self::$class_names[$field_category];

		if ($class !== NULL) {
			$check = $class::where([ 'id' => $id ])->first();
		} else {
			$check = NULL;
		}

		return $check;
	}


	public static function getDefinitions(): ?array
    {
		if (self::$all_definitions !== NULL) {
			return self::$all_definitions;
		}

		self::$all_definitions = [];

		foreach (self::$class_names as $class) {
			self::$all_definitions = array_merge(self::$all_definitions, $class::getDefinitionsOfCategory());
		}

		return self::$all_definitions;
	}


	public static function getDefinitionsOfCategoryOptions(): array
    {
		$return = [];

		foreach (static::getDefinitionsOfCategory() as $definition) {
			$return[$definition['field_key']] = $definition['txt'];
		}

		return $return;
	}

	public static function getCategoryForFieldKey(string $field_key): int
    {
		foreach (self::getDefinitions() as $definition) {
			if ($definition['field_key'] == $field_key) {
				return $definition['field_category'];
			}
		}

		return 0;
	}

	public function getDefinition(): array
    {
		foreach (static::getDefinitionsOfCategory() as $definition) {

			$definition["field_name"] = $definition["txt"];
			$definition["field_id"] = $definition["field_key"];

			if ($definition['field_key'] == $this->field_key) {
				return $definition;
			}
		}

		return [];
	}

	public function getDefinitionValues(): array
    {
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
	protected ?int $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected int $parent_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 *
	 */
	protected string|int $field_key = 1;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 */
	protected string $check_value = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected int $operator = self::OP_EQUALS;
	/**
	 * @var bool
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 */
	protected bool $negated = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected int $owner = 6;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 */
	protected int $status = self::STATUS_ACTIVE;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected int $create_date;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 */
	protected int $update_date;


	/**
	 * @param $field_name
	 *
	 * @return string|null
	 */
	public function sleep($field_name) {
		switch ($field_name) {
			case 'create_date':
			case 'update_date':
				return date(UserDefaultsConfig::SQL_DATE_FORMAT, $this->{$field_name});
				break;
		}

		return NULL;
	}


	/**
	 * @param $field_name
	 * @param $field_value
	 *
	 * @return int|bool|null
     */
	public function wakeUp($field_name, $field_value): int|bool|null
    {
		switch ($field_name) {
			case 'create_date':
			case 'update_date':
				return strtotime($field_value);
				break;
		}

		return NULL;
	}

	public function update(): void
    {
		$this->setOwner($this->user->getId());
		$this->setUpdateDate(time());
		parent::update();
	}

	public function create(): void
    {
		$this->setOwner($this->user->getId());
		$this->setUpdateDate(time());
		$this->setCreateDate(time());
		parent::create();
	}

	public function setCheckValue(string $check_value): void
    {
		$this->check_value = $check_value;
	}

	public function setCheckValues(array $check_values): void
    {
		$this->check_value = implode(self::CHECK_SPLIT, array_map(function ($check_value) {
			return trim($check_value);
		}, $check_values));
	}


	public function getCheckValue(): string
    {
		return $this->check_value;
	}


	/**
	 * @return string[]
	 */
	public function getCheckValues(): array
    {
		return array_map(function ($check_value) {
			return trim($check_value);
		}, explode(self::CHECK_SPLIT, $this->check_value));
	}

	public function setFieldKey(string $field_key): void
    {
		$this->field_key = $field_key;
	}


	public function getFieldKey(): int|string
    {
		return $this->field_key;
	}

	public function setOperator(int $operator): void
    {
		$this->operator = $operator;
	}

	public function getOperator(): int
    {
		return $this->operator;
	}

	public function setCreateDate(int $create_date): void
    {
		$this->create_date = $create_date;
	}

	public function getCreateDate(): int
    {
		return $this->create_date;
	}


	public function setId(int $id): void
    {
		$this->id = $id;
	}

	public function getId(): int
    {
		return $this->id;
	}

	public function setOwner(int $owner): void
    {
		$this->owner = $owner;
	}

	public function getOwner(): int
    {
		return $this->owner;
	}

	public function setUpdateDate(int $update_date): void
    {
		$this->update_date = $update_date;
	}

	public function getUpdateDate(): int
    {
		return $this->update_date;
	}

	public function setStatus(int $status): void
    {
		$this->status = $status;
	}

	public function getStatus(): int
    {
		return $this->status;
	}

	public function setParentId(int $parent_id): void
    {
		$this->parent_id = $parent_id;
	}

	public function getParentId(): int
    {
		return $this->parent_id;
	}

	public function isNegated(): bool
    {
		return $this->negated;
	}

	public function setNegated(bool $negated): void
    {
		$this->negated = $negated;
	}


	public function isValid(ilObjUser $user): bool
    {

		//more than one $value possible because of cascade-select plugin...
		$values = array_map(function ($value) {
			return trim($value);
		}, $this->getFieldValue($user));

		$check_values = $this->getCheckValues();
		$valid = false;
		foreach ($check_values as $key => $check_value) {
            if (empty($check_value) && !in_array($this->getOperator(), [self::OP_IS_EMPTY, self::OP_NOT_IS_EMPTY])) {
                continue;
            }

            if (count($values) > 1) {
                $value = $values[$key];
            } else {
                $value = reset($values);
            }

            switch ($this->getOperator()) {
				case self::OP_EQUALS:
					$valid = (strtolower($value) === strtolower($check_value));
					break;

				case self::OP_NOT_EQUALS:
					$valid = (strtolower($value) !== strtolower($check_value));
					break;

				case self::OP_STARTS_WITH:
					$valid = (strpos(strtolower($value), strtolower($check_value)) === 0);
					break;

				case self::OP_NOT_STARTS_WITH:
					$valid = (strpos(strtolower($value), strtolower($check_value)) !== 0);
					break;

				case self::OP_ENDS_WITH:
					$valid = (strpos(strtolower($value), strtolower($check_value)) === (strlen($value) - strlen($check_value)));
					break;

				case self::OP_NOT_ENDS_WITH:
					$valid = (strpos(strtolower($value), strtolower($check_value)) !== (strlen($value) - strlen($check_value)));
					break;

				case self::OP_CONTAINS:
					$valid = (strpos(strtolower($value), strtolower($check_value)) !== false);
					break;
				case self::OP_NOT_CONTAINS:
					$valid = (strpos(strtolower($value), strtolower($check_value)) === false);
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

            // it's an AND condition
            if (!$valid) {
                break;
            }
		}

		$valid = (!$this->isNegated() === $valid);

		return $valid;
	}

	public final function getFieldCategory(): int|string
    {
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

	protected static abstract function getDefinitionsOfCategory(): array;

	protected abstract function getFieldValue(ilObjUser $user): array;
}
