<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class Courses
 *
 * @package srag\Plugins\UserDefaults\Access
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Courses {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const TYPE_CRS = "crs";
	/**
	 * @var self
	 */
	protected static $instance = NULL;


	/**
	 * @return self
	 */
	public static function getInstance()/*: self*/ {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Courses constructor
	 */
	private function __construct() {

	}


	/**
	 * @param int $category_ref_id
	 *
	 * @return int[]
	 */
	public function getCoursesOfCategory($category_ref_id) {
		return self::dic()->tree()->getSubTree(self::dic()->tree()->getNodeData($category_ref_id), false, self::TYPE_CRS);
	}
}
