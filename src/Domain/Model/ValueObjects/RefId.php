<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class RefId
{
    private function __construct(public int $value)
    {

    }

    public static function new(int $int): RefId
    {
        return new self($int);
    }
}