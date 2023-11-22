<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ActiveRecord;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Config\UserDefaultsConfig;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

class UDFCheckOld extends ActiveRecord {

	use UserDefaultsTrait;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const TABLE_NAME = 'usr_def_checks';
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getConnectorContainerName(): string
    {
		return static::TABLE_NAME;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public static function returnDbTableName(): string
    {
		return static::TABLE_NAME;
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
	 *
	 * @deprecated
	 */
	protected int $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 *
	 * @deprecated
	 */
	protected int $parent_id = 0;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 *
	 * @deprecated
	 *
	 */
	protected string|int $field_key = 1;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 * @db_is_notnull  true
	 *
	 * @deprecated
	 */
	protected int $field_category = UDFCheckUser::FIELD_CATEGORY;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 *
	 * @deprecated
	 */
	protected string $check_value = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 *
	 * @deprecated
	 */
	protected int $operator = UDFCheck::OP_EQUALS;
	/**
	 * @var bool
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 *
	 * @deprecated
	 */
	protected bool $negated = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 *
	 * @deprecated
	 */
	protected int $owner = 6;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 *
	 * @deprecated
	 */
	protected int $status = UDFCheck::STATUS_ACTIVE;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 *
	 * @deprecated
	 */
	protected int $create_date;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 *
	 * @deprecated
	 */
	protected int $update_date;


	/**
	 * @deprecated
	 */
	public function update(): void
    {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		parent::update();
	}


	/**
	 * @deprecated
	 */
	public function create(): void
    {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		$this->setCreateDate(time());
		parent::create();
	}


	/**
	 * @param string $check_value
	 *
	 * @deprecated
	 */
	public function setCheckValue($check_value): void
    {
		$this->check_value = $check_value;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getCheckValue(): string
    {
		return $this->check_value;
	}


	/**
	 * @param string $field_key
	 *
	 * @deprecated
	 */
	public function setFieldKey($field_key): void
    {
		$this->field_key = $field_key;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getFieldKey(): int|string
    {
		return $this->field_key;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getFieldCategory(): int
    {
		return $this->field_category;
	}


	/**
	 * @param int $field_category
	 *
	 * @deprecated
	 */
	public function setFieldCategory($field_category): void
    {
		$this->field_category = $field_category;
	}


	/**
	 * @param int $operator
	 *
	 * @deprecated
	 */
	public function setOperator($operator): void
    {
		$this->operator = $operator;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getOperator(): int
    {
		return $this->operator;
	}


	/**
	 * @param int $create_date
	 *
	 * @deprecated
	 */
	public function setCreateDate($create_date): void
    {
		$this->create_date = $create_date;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getCreateDate(): int
    {
		return $this->create_date;
	}


	/**
	 * @param int $id
	 *
	 * @deprecated
	 */
	public function setId(int $id): void
    {
		$this->id = $id;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getId(): int
    {
		return $this->id;
	}


	/**
	 * @param int $owner
	 *
	 * @deprecated
	 */
	public function setOwner(int $owner): void
    {
		$this->owner = $owner;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getOwner(): int
    {
		return $this->owner;
	}


	/**
	 * @param int $update_date
	 *
	 * @deprecated
	 */
	public function setUpdateDate(int $update_date): void
    {
		$this->update_date = $update_date;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getUpdateDate(): int
    {
		return $this->update_date;
	}


	/**
	 * @param int $status
	 *
	 * @deprecated
	 */
	public function setStatus(int $status): void
    {
		$this->status = $status;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getStatus(): int
    {
		return $this->status;
	}


	/**
	 * @param int $parent_id
	 *
	 * @deprecated
	 */
	public function setParentId(int $parent_id): void
    {
		$this->parent_id = $parent_id;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getParentId(): int
    {
		return $this->parent_id;
	}


	/**
	 * @return boolean
	 *
	 * @deprecated
	 */
	public function isNegated(): bool
    {
		return $this->negated;
	}


	/**
	 * @param boolean $negated
	 *
	 * @deprecated
	 */
	public function setNegated(bool $negated): void
    {
		$this->negated = $negated;
	}


	/**
	 * @param $field_name
	 *
	 * @return mixed|null|string
	 *
	 * @deprecated
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
     *
	 * @deprecated
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
}
