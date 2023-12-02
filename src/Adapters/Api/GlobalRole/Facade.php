<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\GlobalRole;

use srag\Plugins\UserDefaults\Domain\Ports\GlobalRoleService;

class Facade
{
    private function __construct(private GlobalRoleService $globalRoleService)
    {

    }

    public static function new(GlobalRoleService $globalRoleService): self
    {
        return new self($globalRoleService);
    }

    /**
     * @return Responses\GlobalRole[]
     */
    public function findAll(): array
    {
        $globalRoles = $this->globalRoleService->findAll();
        $courseResponses = [];

        foreach ($globalRoles as $globalRole) {
            $courseResponses[] = Responses\GlobalRole::formDomain($globalRole);
        }
        return $courseResponses;
    }
}