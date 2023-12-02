<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\GlobalRole\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class GlobalRole
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(Model\GlobalRole $globalRole): self
    {
        return new self($globalRole->objId->value, $globalRole->title->value);
    }
}