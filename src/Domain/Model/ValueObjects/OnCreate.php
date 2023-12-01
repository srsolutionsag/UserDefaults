<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class OnCreate
{
    private function __construct(public bool $value)
    {

    }

    public static function new(bool $bool): OnCreate
    {
        return new self($bool);
    }
}