<?php

namespace srag\Plugins\UserDefaults\Utils;

use srag\Plugins\UserDefaults\Access\Access;

/**
 * Trait UserDefaultsTrait
 *
 * @package srag\Plugins\UserDefaults\Utils
 */
trait UserDefaultsTrait {

	/**
	 * @return Access
	 */
	protected static function access() {
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
}
