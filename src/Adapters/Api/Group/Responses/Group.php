<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Group\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class Group
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(Model\Group $group): self
    {
        return new self($group->id->objId->value, $group->title->value);
    }
}