<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Group;

use srag\Plugins\UserDefaults\Domain\Ports\GroupService;

class GroupsFacade
{
    private function __construct(private GroupService $groupService)
    {

    }

    public static function new(GroupService $groupService): GroupsFacade
    {
        return new self($groupService);
    }

    /**
     * @return GroupResponse[]
     */
    public function get(): array
    {
        $groups = $this->groupService->findAll();
        $courseResponses = [];

        foreach ($groups as $group) {
            $courseResponses[] = GroupResponse::formDomain($group);
        }
        return $courseResponses;
    }
}