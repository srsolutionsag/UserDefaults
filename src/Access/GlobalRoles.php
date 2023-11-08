<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class GlobalRoles
 *
 * @package srag\Plugins\UserDefaults\Access
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
final class GlobalRoles
{
    use DICTrait;
    use UserDefaultsTrait;
    const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    const TYPE_GLOBAL_ROLE = "role";
    protected static ?GlobalRoles $instance = NULL;


    /**
     * @return self
     */
    public static function getInstance(): ?GlobalRoles {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * LocalRole constructor
     */
    private function __construct() {

    }
}