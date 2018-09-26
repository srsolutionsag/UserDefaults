<?php

namespace srag\Plugins\UserDefaults\Config;

use ilUserDefaultsPlugin;
use srag\ActiveRecordConfig\ActiveRecordConfig;
use usrdefRemoveDataConfirm;

/**
 * Class Config
 *
 * @package srag\Plugins\UserDefaults\Config
 */
class Config extends ActiveRecordConfig {

	const TABLE_NAME = 'usr_def_config';
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;


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
