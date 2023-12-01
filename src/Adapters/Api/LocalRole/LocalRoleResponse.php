<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\LocalRole;

use srag\Plugins\UserDefaults\Domain\Model\LocalRole;

class LocalRoleResponse
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function fromDomain(LocalRole $localRole): LocalRoleResponse
    {
        return new self($localRole->objId->value, $localRole->title->value);
    }
}