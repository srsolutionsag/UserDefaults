<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\LocalRole;

use srag\Plugins\UserDefaults\Domain\Ports\LocalRoleService;

class Facade
{
    private function __construct(private LocalRoleService $localRoleService)
    {

    }

    public static function new(LocalRoleService $localRoleService): Facade
    {
        return new self($localRoleService);
    }

    /**
     * @return Responses\LocalRole[]
     */
    public function findAll(): array
    {
        $localRoles = $this->localRoleService->findAll();
        $courseResponses = [];

        foreach ($localRoles as $localRole) {
            $courseResponses[] = Responses\LocalRole::fromDomain($localRole);
        }
        return $courseResponses;
    }
}