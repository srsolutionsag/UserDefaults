<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class IsActive
{
    private function __construct(public bool $value)
    {

    }

    public static function new(bool $bool): IsActive
    {
        return new self($bool);
    }
}