<?php

namespace srag\Plugins\UserDefaults\Access;

use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

final class Access
{
    use UserDefaultsTrait;
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    protected static ?Access $instance = null;

    public static function getInstance(): ?Access
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
