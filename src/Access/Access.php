<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;

final class Access
{
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    private static ?Access $instance = null;

    public static function getInstance(): Access
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    private function __construct()
    {
    }
}
