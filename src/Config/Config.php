<?php

namespace srag\Plugins\UserDefaults\Config;

use ilUserDefaultsPlugin;
use srag\ActiveRecordConfig\ActiveRecordConfig;
use usrdefRemoveDataConfirm;

/**
 * Class Config
 *
 * @package srag\Plugins\UserDefaults\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class Config extends ActiveRecordConfig {

	const TABLE_NAME = "usr_def_config";
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const KEY_CATEGORY_REF_ID = "category_ref_id";
	const DEFAULT_CATEGORY_REF_ID = 0;


	/**
	 * @return int
	 */
	public static function getCategoryRefId() {
		return self::getIntegerValue(self::KEY_CATEGORY_REF_ID, self::DEFAULT_CATEGORY_REF_ID);
	}


	/**
	 * @param int $category_ref_id
	 */
	public static function setCategoryRefId($category_ref_id) {
		self::setIntegerValue(self::KEY_CATEGORY_REF_ID, $category_ref_id);
	}


	/**
	 * @return bool|null
	 */
	public static function getUninstallRemovesData()/*: ?bool*/ {
		return self::getXValue(usrdefRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, usrdefRemoveDataConfirm::DEFAULT_UNINSTALL_REMOVES_DATA);
	}


	/**
	 * @param bool $uninstall_removes_data
	 */
	public static function setUninstallRemovesData(/*bool*/
		$uninstall_removes_data)/*: void*/ {
		self::setBooleanValue(usrdefRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA, $uninstall_removes_data);
	}


	/**
	 *
	 */
	public static function removeUninstallRemovesData()/*: void*/ {
		self::removeName(usrdefRemoveDataConfirm::KEY_UNINSTALL_REMOVES_DATA);
	}
}
