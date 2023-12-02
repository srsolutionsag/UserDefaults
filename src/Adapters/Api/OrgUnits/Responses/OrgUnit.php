<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class OrgUnit
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(Model\OrgUnit $orgUnit): self
    {
        return new self($orgUnit->id->objId->value, $orgUnit->title->value);
    }
}