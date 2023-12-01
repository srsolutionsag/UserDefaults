<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\LocalRole;

use srag\Plugins\UserDefaults\Domain\Ports\LocalRoleService;

class LocalRolesFacade
{
    private function __construct(private LocalRoleService $localRoleService)
    {

    }

    public static function new(LocalRoleService $localRoleService): LocalRolesFacade
    {
        return new self($localRoleService);
    }

    /**
     * @return LocalRoleResponse[]
     */
    public function get(): array
    {
        $localRoles = $this->localRoleService->findAll();
        $courseResponses = [];

        foreach ($localRoles as $localRole) {
            $courseResponses[] = LocalRoleResponse::fromDomain($localRole);
        }
        return $courseResponses;
    }
}