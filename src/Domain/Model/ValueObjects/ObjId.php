<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class ObjId
{
    private function __construct(public int $value)
    {

    }

    public static function new(int $int): self
    {
        return new self($int);
    }
}