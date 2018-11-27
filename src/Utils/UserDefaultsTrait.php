<?php

namespace srag\Plugins\UserDefaults\Utils;

use srag\Plugins\UserDefaults\Access\Access;
use srag\Plugins\UserDefaults\Access\Ilias;

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
	 * @return Ilias
	 */
	protected static function ilias()/*: Ilias*/ {
		return Ilias::getInstance();
	}


	/**
	 * CustomUserFieldsHelper is only available for DHBW Spec!
	 *
	 * @return bool
	 */
	protected static function isCustomUserFieldsHelperAvailable() {
		if (file_exists("./Services/User/classes/class.ilCustomUserFieldsHelper.php")) {
			require_once "./Services/User/classes/class.ilCustomUserFieldsHelper.php";

			return true;
		}

		return false;
	}
}
