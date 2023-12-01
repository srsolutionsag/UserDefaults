<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\StudyProgramme;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class StudyProgrammesApiEndpoint
{

    private function __construct(
        private StudyProgrammesFacade $studyProgrammes
    )
    {

    }

    public static function new(Configs $configs): self
    {
        return new self(StudyProgrammesFacade::new($configs->studyProgrammeService));
    }


    public function get(): array
    {
        return $this->studyProgrammes->get();
    }
}