<?php

namespace srag\Plugins\UserDefaults\Utils;

/**
 * Trait UserDefaultsTrait
 *
 * @package srag\Plugins\UserDefaults\Utils
 */
trait UserDefaultsTrait {

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
