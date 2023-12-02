<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class OrgUnitPositions
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

    public static function new(int $id, string $title): self
    {
        return new self($id, $title);
    }
}