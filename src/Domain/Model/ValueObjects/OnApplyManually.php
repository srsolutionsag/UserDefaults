<?php

namespace srag\Plugins\UserDefaults\Domain\Model\ValueObjects;

class OnApplyManually
{
    private function __construct(public bool $value)
    {

    }

    public static function new(bool $bool): OnApplyManually
    {
        return new self($bool);
    }
}