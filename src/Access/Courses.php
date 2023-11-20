<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

final class Courses {

	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const TYPE_CRS = "crs";
	protected static ?Courses $instance = NULL;
    private \ilTree $repositoryTree;

    public static function getInstance(): ?Courses {
		if (self::$instance === NULL) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Courses constructor
	 */
	private function __construct() {
        global $DIC;
        $this->repositoryTree = $DIC->repositoryTree();
	}


	/**
	 * @return int[]
	 */
	public function getCoursesOfCategory(int $category_ref_id): array
    {
		return self::dic()->repositoryTree()->getSubTree($this->repositoryTree->getNodeData($category_ref_id), false, [self::TYPE_CRS]);
	}
}
