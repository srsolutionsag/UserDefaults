<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\LocalRole;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{

    private function __construct(
        private Facade $localRoles
    )
    {

    }

    public static function new(Configs $configs): Api
    {
        return new self(Facade::new($configs->localRoleService));
    }


    public function findAll(): array
    {
        return $this->localRoles->findAll();
    }
}