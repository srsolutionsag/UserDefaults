<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\StudyProgramme;

use srag\Plugins\UserDefaults\Domain\Model\StudyProgramme;

class StudyProgrammeResponse
{
    private function __construct(
        public int $id, public string $title
    )
    {

    }

    public static function formDomain(StudyProgramme $studyProgramme): self
    {
        return new self($studyProgramme->id->objId->value, $studyProgramme->title->value);
    }
}