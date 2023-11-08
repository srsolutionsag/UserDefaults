<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class Access
 *
 * @package srag\Plugins\UserDefaults\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Access {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected static ?Access $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance(): ?Access {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {

	}
}
