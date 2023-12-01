<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class Title
{
    private function __construct(public string $value)
    {

    }

    public static function new(string $string): Title
    {
        return new self($string);
    }
}