<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Requests;

class RenderForm
{
    private function __construct(
        public object $parentIliasGui, public ?int $assignmentProcessId
    )
    {

    }


    public static function new(object $parentIliasGui, ?int $assignmentProcessId = null): self
    {
        return new self($parentIliasGui, $assignmentProcessId);
    }
}