<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

final class Ilias {
    use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected static ?Ilias $instance = NULL;


	public static function getInstance(): ?Ilias {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {

	}

	public function courses(): Courses {
		return Courses::getInstance();
	}

	public function categories(): Categories {
		return Categories::getInstance();
	}
}
