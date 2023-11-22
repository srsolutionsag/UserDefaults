<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

final class GlobalRoles
{
    use UserDefaultsTrait;
    const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    const TYPE_GLOBAL_ROLE = "role";
    protected static ?GlobalRoles $instance = NULL;


    public static function getInstance(): ?GlobalRoles {
        if (self::$instance === NULL) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct() {

    }
}