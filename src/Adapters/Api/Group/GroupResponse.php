<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Group;

use srag\Plugins\UserDefaults\Domain\Model\Group;

class GroupResponse
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(Group $group): self
    {
        return new self($group->id->objId->value, $group->title->value);
    }
}