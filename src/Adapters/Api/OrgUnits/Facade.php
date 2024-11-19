<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits;

use ilOrgUnitPosition;
use srag\Plugins\UserDefaults\Domain\Ports\OrgUnitService;
use ilOrgUnitPositionLocalDIC;

class Facade
{
    private function __construct(private OrgUnitService $orgUnitService)
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
            $courseResponses[] = Responses\OrgUnit::formDomain($course);
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
            $orguPositions[] = Responses\OrgUnitPositions::new($position->getId(), $position->getTitle());
        }
        return $orguPositions;
    }
}
