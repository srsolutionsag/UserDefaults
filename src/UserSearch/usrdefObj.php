<?php

namespace srag\Plugins\UserDefaults\UserSearch;

use ActiveRecord;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 *
 * @deprecated TODO: Remove this class
 */
class usrdefObj extends ActiveRecord {

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
	 * @deprecated
	 */
	public function getConnectorContainerName(): string
    {
		return self::TABLE_NAME;
	}


	/**
	 * @deprecated
	 */
	public static function returnDbTableName(): string
    {
		return self::TABLE_NAME;
	}


	/**
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 * @con_is_primary true
	 * @con_is_unique  true
	 *
	 * @deprecated
	 */
	protected int $obj_id;
	/**
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    4
	 *
	 * @deprecated
	 */
	protected string $type;
	/**
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    255
	 *
	 * @deprecated
	 */
	protected string $title;
	/**
	 * @con_has_field true
	 * @con_fieldtype text
	 * @con_length    128
	 *
	 * @deprecated
	 */
	protected string $description;
	/**
	 * @con_has_field  true
	 * @con_fieldtype  integer
	 * @con_length     4
	 * @con_is_notnull true
	 *
	 * @deprecated
	 */
	protected int $owner;
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
	 * @deprecated
	 */
	public function getObjId(): int {
		return $this->obj_id;
	}


	/**
	 * @deprecated
	 */
	public function setObjId(int $obj_id): void
    {
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
