<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

final class Access {
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected static ?Access $instance = NULL;

	public static function getInstance(): ?Access {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

	}
}
