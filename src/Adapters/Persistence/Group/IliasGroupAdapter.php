<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\Group;

use srag\Plugins\UserDefaults\Domain\Model\Group;

class IliasGroupAdapter
{

    private function __construct(public int $objId, public int $refId, public string $title)
    {

    }

    public static function new(int $objId, int $refId, string $title): self
    {
        return new self($objId, $refId, $title);
    }

    public function toDomain(): Group
    {
        return Group::new($this->objId, $this->refId, $this->title);
    }
}