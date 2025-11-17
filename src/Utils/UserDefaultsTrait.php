<?php

namespace srag\Plugins\UserDefaults\Utils;

trait UserDefaultsTrait
{
    /**
     * CustomUserFieldsHelper is only available for DHBW Spec!
     */
    protected static function isCustomUserFieldsHelperAvailable(): bool
    {
        return file_exists("./components/ILIAS/User/classes/class.ilCustomUserFieldsHelper.php");
    }
}
