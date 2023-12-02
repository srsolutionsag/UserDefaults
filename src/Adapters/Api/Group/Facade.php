<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Group;

use srag\Plugins\UserDefaults\Domain\Ports\GroupService;

class Facade
{
    private function __construct(private GroupService $groupService)
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
            $courseResponses[] = Responses\Group::formDomain($group);
        }
        return $courseResponses;
    }
}