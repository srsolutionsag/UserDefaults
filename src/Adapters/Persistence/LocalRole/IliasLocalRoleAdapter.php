<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\LocalRole;

use srag\Plugins\UserDefaults\Domain\Model\LocalRole;

class IliasLocalRoleAdapter
{

    private function __construct(public int $objId, public string $title)
    {

    }

    public static function new(int $objId, string $title): self
    {
        return new self($objId, $title);
    }

    public function toDomain(): LocalRole
    {
        return LocalRole::new($this->objId, $this->title);
    }
}