<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\GlobalRole;

use srag\Plugins\UserDefaults\Domain\Model\GlobalRole;

class IliasGlobalRoleAdapter
{

    private function __construct(public int $objId, public string $title)
    {

    }

    public static function new(int $objId, string $title): self
    {
        return new self($objId, $title);
    }

    public function toDomain(): GlobalRole
    {
        return GlobalRole::new($this->objId, $this->title);
    }
}