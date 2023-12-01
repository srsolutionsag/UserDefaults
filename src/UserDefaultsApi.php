<?php

namespace srag\Plugins\UserDefaults;

use srag\Plugins\UserDefaults\Adapters\Api;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class UserDefaultsApi
{

    private function __construct(
        private Configs $configs
    )
    {

    }

    public static function new(): UserDefaultsApi
    {
        return new self(Configs::new());
    }


    public function assignmentProcess(): Api\AssignmentProcess\Api
    {
        return Api\AssignmentProcess\Api::new($this->configs);
    }

    public function ui(): Api\Ui\Api
    {
        return Api\Ui\Api::new($this->configs);
    }
}