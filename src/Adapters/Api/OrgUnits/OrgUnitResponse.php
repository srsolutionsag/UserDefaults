<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits;

use srag\Plugins\UserDefaults\Domain\Model\OrgUnit;

class OrgUnitResponse
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(OrgUnit $orgUnit): self
    {
        return new self($orgUnit->id->objId->value, $orgUnit->title->value);
    }
}