<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\StudyProgramme;

use srag\Plugins\UserDefaults\Domain\Ports\StudyProgrammeService;

class StudyProgrammesFacade
{
    private function __construct(private StudyProgrammeService $studyProgrammeService)
    {

    }

    public static function new(StudyProgrammeService $studyProgrammeService): self
    {
        return new self($studyProgrammeService);
    }

    /**
     * @return StudyProgrammeResponse[]
     */
    public function get(): array
    {
        $studyProgrammes = $this->studyProgrammeService->findAll();
        $studyProgrammeResponses = [];

        foreach ($studyProgrammes as $studyProgramme) {
            $studyProgrammeResponses[] = StudyProgrammeResponse::formDomain($studyProgramme);
        }
        return $studyProgrammeResponses;
    }
}