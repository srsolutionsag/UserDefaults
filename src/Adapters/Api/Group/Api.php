<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Group;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{

    private function __construct(
        private Facade $groups
    )
    {

    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs->groupService));
    }


    public function findAll(): array
    {
        return $this->groups->findAll();
    }
}