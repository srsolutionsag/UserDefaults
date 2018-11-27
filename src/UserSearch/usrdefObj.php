<?php

namespace srag\Plugins\UserDefaults\UserSearch;

use ActiveRecord;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class usrdefObj
 *
 * @package    srag\Plugins\UserDefaults\UserSearch
 *
 * @author     Fabian Schmid <fs@studer-raimann.ch>
 * @version    2.0.6
 *
 * @deprecated TODO: Remove this class
 */
class usrdefObj extends ActiveRecord {

	use DICTrait;
	use UserDefaultsTrait;
	/**
	 * @var string
	 *
	 * @deprecated
	 */
	const TABLE_NAME = "object_data";
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
		return self::TABLE_NAME;
	}


	/**
	 * @return string
	 *
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
	 *
	 * @deprecated
	 */
	protected $obj_id;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected $type;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    255
	 *
	 * @deprecated
	 */
	protected $title;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    128
	 *
	 * @deprecated
	 */
	protected $description;
	/**
	 * @var
	 *
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected $owner;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype
	 * @con_length
	 *
	 * @deprecated
	 */
	protected $create_date;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype
	 * @con_length
	 *
	 * @deprecated
	 */
	protected $last_update;
	/**
	 * @var
	 *
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    50
	 *
	 * @deprecated
	 */
	protected $import_id;


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getObjId() {
		return $this->obj_id;
	}


	/**
	 * @param mixed $obj_id
	 *
	 * @deprecated
	 */
	public function setObjId($obj_id) {
		$this->obj_id = $obj_id;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getType() {
		return $this->type;
	}


	/**
	 * @param mixed $type
	 *
	 * @deprecated
	 */
	public function setType($type) {
		$this->type = $type;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getTitle() {
		return $this->title;
	}


	/**
	 * @param mixed $title
	 *
	 * @deprecated
	 */
	public function setTitle($title) {
		$this->title = $title;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getDescription() {
		return $this->description;
	}


	/**
	 * @param mixed $description
	 *
	 * @deprecated
	 */
	public function setDescription($description) {
		$this->description = $description;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getOwner() {
		return $this->owner;
	}


	/**
	 * @param mixed $owner
	 *
	 * @deprecated
	 */
	public function setOwner($owner) {
		$this->owner = $owner;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getCreateDate() {
		return $this->create_date;
	}


	/**
	 * @param mixed $create_date
	 *
	 * @deprecated
	 */
	public function setCreateDate($create_date) {
		$this->create_date = $create_date;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getLastUpdate() {
		return $this->last_update;
	}


	/**
	 * @param mixed $last_update
	 *
	 * @deprecated
	 */
	public function setLastUpdate($last_update) {
		$this->last_update = $last_update;
	}


	/**
	 * @return mixed
	 *
	 * @deprecated
	 */
	public function getImportId() {
		return $this->import_id;
	}


	/**
	 * @param mixed $import_id
	 *
	 * @deprecated
	 */
	public function setImportId($import_id) {
		$this->import_id = $import_id;
	}
}
