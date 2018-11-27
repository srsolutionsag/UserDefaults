<?php

namespace srag\Plugins\UserDefaults\Config;

use ilUserDefaultsPlugin;
use srag\ActiveRecordConfig\UserDefaults\ActiveRecordConfig;

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
	/**
	 * @var array
	 */
	protected static $fields = [
		self::KEY_CATEGORY_REF_ID => self::TYPE_INTEGER
	];
}
