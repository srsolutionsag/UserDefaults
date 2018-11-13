<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ActiveRecord;
use ilUserDefaultsPlugin;
use srag\ActiveRecordConfig\UserDefaults\ActiveRecordConfig;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class UDFCheckOld
 *
 * @package srag\Plugins\UserDefaults\UDFCheck
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 *
 * @deprecated
 */
class UDFCheckOld extends ActiveRecord {

	use DICTrait;
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
	protected $id = 0;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 *
	 * @deprecated
	 */
	protected $parent_id = 0;
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
	protected $field_key = 1;
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
	protected $field_category = UDFCheckUser::FIELD_CATEGORY;
	/**
	 * @var string
	 *
	 * @con_has_field  true
	 * @con_fieldtype  text
	 * @con_length     256
	 *
	 * @deprecated
	 */
	protected $check_value = '';
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 *
	 * @deprecated
	 */
	protected $operator = UDFCheck::OP_EQUALS;
	/**
	 * @var bool
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     1
	 *
	 * @deprecated
	 */
	protected $negated = false;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 *
	 * @deprecated
	 */
	protected $owner = 6;
	/**
	 * @var int
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     8
	 *
	 * @deprecated
	 */
	protected $status = UDFCheck::STATUS_ACTIVE;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 *
	 * @deprecated
	 */
	protected $create_date;
	/**
	 * @var int
	 *
	 * @db_has_field        true
	 * @db_fieldtype        timestamp
	 * @db_is_notnull       true
	 *
	 * @deprecated
	 */
	protected $update_date;


	/**
	 * @deprecated
	 */
	public function update() {
		$this->setOwner(self::dic()->user()->getId());
		$this->setUpdateDate(time());
		parent::update();
	}


	/**
	 * @deprecated
	 */
	public function create() {
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
	public function setCheckValue($check_value) {
		$this->check_value = $check_value;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getCheckValue() {
		return $this->check_value;
	}


	/**
	 * @param string $field_key
	 *
	 * @deprecated
	 */
	public function setFieldKey($field_key) {
		$this->field_key = $field_key;
	}


	/**
	 * @return string
	 *
	 * @deprecated
	 */
	public function getFieldKey() {
		return $this->field_key;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getFieldCategory() {
		return $this->field_category;
	}


	/**
	 * @param int $field_category
	 *
	 * @deprecated
	 */
	public function setFieldCategory($field_category) {
		$this->field_category = $field_category;
	}


	/**
	 * @param int $operator
	 *
	 * @deprecated
	 */
	public function setOperator($operator) {
		$this->operator = $operator;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getOperator() {
		return $this->operator;
	}


	/**
	 * @param int $create_date
	 *
	 * @deprecated
	 */
	public function setCreateDate($create_date) {
		$this->create_date = $create_date;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getCreateDate() {
		return $this->create_date;
	}


	/**
	 * @param int $id
	 *
	 * @deprecated
	 */
	public function setId($id) {
		$this->id = $id;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getId() {
		return $this->id;
	}


	/**
	 * @param int $owner
	 *
	 * @deprecated
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getOwner() {
		return $this->owner;
	}


	/**
	 * @param int $update_date
	 *
	 * @deprecated
	 */
	public function setUpdateDate($update_date) {
		$this->update_date = $update_date;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getUpdateDate() {
		return $this->update_date;
	}


	/**
	 * @param int $status
	 *
	 * @deprecated
	 */
	public function setStatus($status) {
		$this->status = $status;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getStatus() {
		return $this->status;
	}


	/**
	 * @param int $parent_id
	 *
	 * @deprecated
	 */
	public function setParentId($parent_id) {
		$this->parent_id = $parent_id;
	}


	/**
	 * @return int
	 *
	 * @deprecated
	 */
	public function getParentId() {
		return $this->parent_id;
	}


	/**
	 * @return boolean
	 *
	 * @deprecated
	 */
	public function isNegated() {
		return $this->negated;
	}


	/**
	 * @param boolean $negated
	 *
	 * @deprecated
	 */
	public function setNegated($negated) {
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
	 *
	 * @deprecated
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
}
