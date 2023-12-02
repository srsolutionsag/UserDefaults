<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Course\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class Course
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }


    public static function fromDomain(Model\Course $course): self
    {
        return new self($course->id->objId->value, $course->title->value);
    }
}