<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\StudyProgramme\Responses;

use srag\Plugins\UserDefaults\Domain\Model;

class StudyProgramme
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(Model\StudyProgramme $studyProgramme): self
    {
        return new self($studyProgramme->id->objId->value, $studyProgramme->title->value);
    }
}