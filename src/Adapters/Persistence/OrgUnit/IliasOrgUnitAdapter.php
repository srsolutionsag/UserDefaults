<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\OrgUnit;

use srag\Plugins\UserDefaults\Domain\Model\OrgUnit;

class IliasOrgUnitAdapter
{

    private function __construct(public int $objId, public int $refId, public string $title)
    {

    }

    public static function new(int $objId, int $refId, string $title): self
    {
        return new self($objId, $refId, $title);
    }

    public function toDomain(): OrgUnit
    {
        return OrgUnit::new($this->objId, $this->refId, $this->title);
    }
}