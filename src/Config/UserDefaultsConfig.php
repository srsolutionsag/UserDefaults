<?php

namespace srag\Plugins\UserDefaults\Config;

use ilUserDefaultsPlugin;
use srag\ActiveRecordConfig\UserDefaults\Config\Config;

/**
 * Class Config
 *
 * @package srag\Plugins\UserDefaults\Config
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class UserDefaultsConfig extends Config {

	const TABLE_NAME = "usr_def_config";
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const KEY_CATEGORY_REF_ID = "category_ref_id";
	/**
	 * @var array
	 */
	private static $fields = [
		self::KEY_CATEGORY_REF_ID => self::TYPE_INTEGER
	];

    public static function getField(string $field): int {
        if (array_key_exists($field, self::$fields)) {
            return self::$fields[$field];
        }

        throw new \ilException("UserDefaults configuration field '$field' not found");
    }
}
