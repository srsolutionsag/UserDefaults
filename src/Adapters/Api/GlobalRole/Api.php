<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\GlobalRole;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;
use srag\Plugins\UserDefaults\Domain\Ports\GlobalRoleService;

class Api
{

    private function __construct(
        private Facade $globalRoles
    )
    {

    }

    public static function new(Configs $configs): self
    {
        return new self(Facade::new($configs->globalRoleService));
    }


    public function findAll(): array
    {
        return $this->globalRoles->findAll();
    }
}