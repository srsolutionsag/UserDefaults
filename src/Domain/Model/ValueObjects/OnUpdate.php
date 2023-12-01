<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class OnUpdate
{
    private function __construct(public bool $value)
    {

    }

    public static function new(bool $bool): OnUpdate
    {
        return new self($bool);
    }
}