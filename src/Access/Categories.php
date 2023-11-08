<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class Categories
 *
 * @package srag\Plugins\UserDefaults\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Categories {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const TYPE_CAT = "cat";
	protected static ?Categories $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance(): ?Categories {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Categories constructor
	 */
	private function __construct() {

	}

	/**
	 * @return int[]
	 */
	public function getCategoriesOfCategory(int $category_ref_id): array
    {
		return self::dic()->repositoryTree()->getSubTree(self::dic()->repositoryTree()->getNodeData($category_ref_id), false, [self::TYPE_CAT]);
	}
}
