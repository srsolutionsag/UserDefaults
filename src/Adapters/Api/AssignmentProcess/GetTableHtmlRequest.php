<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess;

class GetTableHtmlRequest
{
    private function __construct(
        public object $parentIliasGui
    )
    {

    }


    public static function new(object $parentIliasGui): self
    {
        return new self($parentIliasGui);
    }
}