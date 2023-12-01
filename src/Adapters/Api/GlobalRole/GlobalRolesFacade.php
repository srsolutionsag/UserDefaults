<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\GlobalRole;

use srag\Plugins\UserDefaults\Domain\Ports\GlobalRoleService;

class GlobalRolesFacade
{
    private function __construct(private GlobalRoleService $globalRoleService)
    {

    }

    public static function new(GlobalRoleService $globalRoleService): self
    {
        return new self($globalRoleService);
    }

    /**
     * @return GlobalRoleResponse[]
     */
    public function get(): array
    {
        $globalRoles = $this->globalRoleService->findAll();
        $courseResponses = [];

        foreach ($globalRoles as $globalRole) {
            $courseResponses[] = GlobalRoleResponse::formDomain($globalRole);
        }
        return $courseResponses;
    }
}