<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class AssignmentProcessId
{
    private function __construct(public int $value)
    {

    }

    public static function new(int $int): AssignmentProcessId
    {
        return new self($int);
    }
}