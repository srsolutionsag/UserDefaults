<?php

namespace srag\Plugins\UserDefaults\UserSearch;

use ActiveRecord;
use ilUserDefaultsPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class usrdefObj
 *
 * @package srag\Plugins\UserDefaults\UserSearch
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 2.0.6
 * @deprecated
 */
class usrdefObj extends ActiveRecord {

	use DICTrait;
	use UserDefaultsTrait;
	const TABLE_NAME = "object_data";
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;


	/**
	 * @return string
	 */
	public function getConnectorContainerName() {
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 * @deprecated
	 */
	public static function returnDbTableName() {
		return self::TABLE_NAME;
	}


	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 * @con_is_primary true
	 * @con_is_unique  true
	 */
	protected $obj_id;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    4
	 */
	protected $type;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    255
	 */
	protected $title;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    128
	 */
	protected $description;
	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 */
	protected $owner;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype
	 * @con_length
	 */
	protected $create_date;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype
	 * @con_length
	 */
	protected $last_update;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    50
	 */
	protected $import_id;


	/**
	 * @return mixed
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param mixed $obj_id
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @return mixed
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param mixed $type
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return mixed
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param mixed $title
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return mixed
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param mixed $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return mixed
	 */
	public function getOwner() {
		return $this->owner;
	}


	/**
	 * @param mixed $owner
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}


	/**
	 * @return mixed
	 */
	public function getCreateDate() {
		return $this->create_date;
	}


	/**
	 * @param mixed $create_date
	 */
	public function setCreateDate($create_date) {
		$this->create_date = $create_date;
	}


	/**
	 * @return mixed
	 */
	public function getLastUpdate() {
		return $this->last_update;
	}


	/**
	 * @param mixed $last_update
	 */
	public function setLastUpdate($last_update) {
		$this->last_update = $last_update;
	}


	/**
	 * @return mixed
	 */
	public function getImportId() {
		return $this->import_id;
	}


	/**
	 * @param mixed $import_id
	 */
	public function setImportId($import_id) {
		$this->import_id = $import_id;
	}
}
