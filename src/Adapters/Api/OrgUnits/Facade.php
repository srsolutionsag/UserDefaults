<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits;

use srag\Plugins\UserDefaults\Adapters\Api\OrgUnits\Responses\OrgUnit;
use srag\Plugins\UserDefaults\Adapters\Api\OrgUnits\Responses\OrgUnitPositions;
use srag\Plugins\UserDefaults\Domain\Ports\OrgUnitService;

class Facade
{
    public $orguPos;

    private function __construct(private readonly OrgUnitService $orgUnitService)
    {
    }

    public static function new(OrgUnitService $orgUnitService): Facade
    {
        return new self($orgUnitService);
    }

    /**
     * @return Responses\OrgUnit[]
     */
    public function findAll(): array
    {
        $courses = $this->orgUnitService->findAll();
        $courseResponses = [];

        foreach ($courses as $course) {
            $courseResponses[] = OrgUnit::formDomain($course);
        }
        return $courseResponses;
    }

    public function findAllPositions(): array
    {
        //ToDO: check if next two lines should be in constructor
        $orgu = \ilOrgUnitLocalDIC::dic();
        $this->orguPos = $orgu["repo.Positions"];

        $orguPositions = [];
        foreach ($this->orguPos->getAllPositions() as $position) {
            $orguPositions[] = OrgUnitPositions::new($position->getId(), $position->getTitle());
        }
        return $orguPositions;
    }
}
