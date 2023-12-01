<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\LocalRole;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class LocalRolesApiEndpoint
{

    private function __construct(
        private LocalRolesFacade $localRoles
    )
    {

    }

    public static function new(Configs $configs): LocalRolesApiEndpoint
    {
        return new self(LocalRolesFacade::new($configs->localRoleService));
    }


    public function get(): array
    {
        return $this->localRoles->get();
    }
}