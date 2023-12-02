<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits;

use ilOrgUnitPosition;
use srag\Plugins\UserDefaults\Domain\Ports\OrgUnitService;

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
        $orguPositions = [];
        foreach (ilOrgUnitPosition::getArray() as $position) {
            $orguPositions[] = Responses\OrgUnitPositions::new($position['id'], $position['title']);
        }
        return $orguPositions;
    }
}