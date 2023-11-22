<?php

namespace srag\Plugins\UserDefaults\Utils;

use srag\Plugins\UserDefaults\Access\Access;
use srag\Plugins\UserDefaults\Access\Ilias;

trait UserDefaultsTrait {

	/**
	 * @return Access
	 */
	protected static function access(): Access {
		return Access::getInstance();
	}


	/**
	 * @return Ilias
	 */
	protected static function ilias(): Ilias {
		return Ilias::getInstance();
	}


	/**
	 * CustomUserFieldsHelper is only available for DHBW Spec!
	 */
	protected static function isCustomUserFieldsHelperAvailable(): bool
    {
		if (file_exists("./Services/User/classes/class.ilCustomUserFieldsHelper.php")) {
			require_once "./Services/User/classes/class.ilCustomUserFieldsHelper.php";

			return true;
		}

		return false;
	}
}