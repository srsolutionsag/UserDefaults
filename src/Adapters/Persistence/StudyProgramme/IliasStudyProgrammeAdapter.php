<?php

namespace srag\Plugins\UserDefaults\Adapters\Persistence\StudyProgramme;

use srag\Plugins\UserDefaults\Domain\Model\StudyProgramme;

class IliasStudyProgrammeAdapter
{

    private function __construct(public int $objId, public int $refId, public string $title)
    {

    }

    public static function new(int $objId, int $refId, string $title): self
    {
        return new self($objId, $refId, $title);
    }

    public function toDomain(): StudyProgramme
    {
        return StudyProgramme::new($this->objId, $this->refId, $this->title);
    }
}