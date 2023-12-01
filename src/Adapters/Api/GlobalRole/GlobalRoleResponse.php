<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\GlobalRole;

use srag\Plugins\UserDefaults\Domain\Model\GlobalRole;

class GlobalRoleResponse
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(GlobalRole $globalRole): self
    {
        return new self($globalRole->objId->value, $globalRole->title->value);
    }
}