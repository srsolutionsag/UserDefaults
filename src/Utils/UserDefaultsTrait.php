<?php

namespace srag\Plugins\UserDefaults\Utils;

use srag\Plugins\UserDefaults\Access\Access;
use srag\Plugins\UserDefaults\Access\Permission;

/**
 * Trait UserDefaultsTrait
 *
 * @package srag\Plugins\UserDefaults\Utils
 */
trait UserDefaultsTrait {

	/**
	 * @return Access
	 */
	protected static function access()/*: Access*/ {
		return Access::getInstance();
	}


	/**
	 * CustomUserFieldsHelper is only available for DHBW Spec!
	 *
	 * @return bool
	 */
	public static function isCustomUserFieldsHelperAvailable() {
		if (file_exists("./Services/User/classes/class.ilCustomUserFieldsHelper.php")) {
			require_once "./Services/User/classes/class.ilCustomUserFieldsHelper.php";

			return true;
		}

		return false;
	}


	/**
	 * @return Permission
	 */
	protected static function permission()/*: Permission*/ {
		return Permission::getInstance();
	}
}
