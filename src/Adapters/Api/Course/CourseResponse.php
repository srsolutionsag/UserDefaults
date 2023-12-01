<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\Course;

use srag\Plugins\UserDefaults\Domain\Model\Course;

class CourseResponse
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }


    public static function fromDomain(Course $course): self
    {
        return new self($course->id->objId->value, $course->title->value);
    }
}