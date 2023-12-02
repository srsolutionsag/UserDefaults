<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\StudyProgramme;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class Api
{

    private function __construct(
        private Facade $studyProgrammes
    )
    {

    }

    public static function new(Configs $configs): self
    {
        return new self(Facade::new($configs->studyProgrammeService));
    }


    public function findAll(): array
    {
        return $this->studyProgrammes->findAll();
    }
}