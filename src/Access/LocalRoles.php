<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

final class LocalRoles {
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const TYPE_LOCAL_ROLE = "role";
	protected static ?LocalRoles $instance = NULL;

	public static function getInstance(): ?LocalRoles {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

	}
}