<?php

namespace srag\Plugins\UserDefaults\Access;

use ilTree;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

final class Categories
{
    use UserDefaultsTrait;

    const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    const TYPE_CAT = "cat";
    protected static ?Categories $instance = NULL;
    private ilTree $repositoryTree;


    public static function getInstance(): ?Categories
    {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Categories constructor
     */
    private function __construct()
    {
        global $DIC;
        $this->repositoryTree = $DIC->repositoryTree();
    }

    /**
     * @return int[]
     */
    public function getCategoriesOfCategory(int $category_ref_id): array
    {
        return $this->repositoryTree->getSubTree($this->repositoryTree->getNodeData($category_ref_id), false, [self::TYPE_CAT]);
    }
}
