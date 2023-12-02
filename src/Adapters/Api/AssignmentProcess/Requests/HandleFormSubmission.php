<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Requests;

class HandleFormSubmission
{
    private function __construct(
        public object $parentIliasGui, public ?int $assignmentProcessId, private $onSuccess
    )
    {

    }

    public function onSuccess(): void
    {
        if (!is_null($this->onSuccess)) {
            call_user_func($this->onSuccess);
        }
    }


    public static function new(object $parentIliasGui, ?int $assignmentProcessId = null, ?callable $onSuccess = null): self
    {
        return new self($parentIliasGui, $assignmentProcessId, $onSuccess);
    }
}