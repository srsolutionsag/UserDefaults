<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\GlobalRole;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;
use srag\Plugins\UserDefaults\Domain\Ports\GlobalRoleService;

class GlobalRolesApiEndpoint
{

    private function __construct(
        private GlobalRolesFacade $globalRoles
    )
    {

    }

    public static function new(Configs $configs): self
    {
        return new self(GlobalRolesFacade::new($configs->globalRoleService));
    }


    public function get(): array
    {
        return $this->globalRoles->get();
    }
}