<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\LocalRole\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class LocalRole
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function fromDomain(Model\LocalRole $localRole): LocalRole
    {
        return new self($localRole->objId->value, $localRole->title->value);
    }
}