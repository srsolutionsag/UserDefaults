<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\Course;

use srag\Plugins\UserDefaults\Domain\Model\Course;

class IliasCourseAdapter
{

    private function __construct(public int $objId, public int $refId, public string $title)
    {

    }

    public static function new(int $objId, int $refId, string $title): self
    {
        return new self($objId, $refId, $title);
    }

    public function toDomain(): Course
    {
        return Course::new($this->objId, $this->refId, $this->title);
    }
}