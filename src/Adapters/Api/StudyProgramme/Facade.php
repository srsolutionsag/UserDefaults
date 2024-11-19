<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\StudyProgramme;

use srag\Plugins\UserDefaults\Adapters\Api\StudyProgramme\Responses\StudyProgramme;
use srag\Plugins\UserDefaults\Domain\Ports\StudyProgrammeService;

class Facade
{
    private function __construct(private readonly StudyProgrammeService $studyProgrammeService)
    {
    }

    public static function new(StudyProgrammeService $studyProgrammeService): self
    {
        return new self($studyProgrammeService);
    }

    /**
     * @return Responses\StudyProgramme[]
     */
    public function findAll(): array
    {
        $studyProgrammes = $this->studyProgrammeService->findAll();
        $studyProgrammeResponses = [];

        foreach ($studyProgrammes as $studyProgramme) {
            $studyProgrammeResponses[] = StudyProgramme::formDomain($studyProgramme);
        }
        return $studyProgrammeResponses;
    }
}
