<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Group;

use srag\Plugins\UserDefaults\Adapters\Api\Group\Responses\Group;
use srag\Plugins\UserDefaults\Domain\Ports\GroupService;

class Facade
{
    private function __construct(private readonly GroupService $groupService)
    {
    }

    public static function new(GroupService $groupService): Facade
    {
        return new self($groupService);
    }

    /**
     * @return Responses\Group[]
     */
    public function findAll(): array
    {
        $groups = $this->groupService->findAll();
        $courseResponses = [];

        foreach ($groups as $group) {
            $courseResponses[] = Group::formDomain($group);
        }
        return $courseResponses;
    }
}
