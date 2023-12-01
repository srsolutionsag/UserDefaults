<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\OrgUnits;

use srag\Plugins\UserDefaults\Domain\Ports\OrgUnitService;

class OrgUnitsFacade
{
    private function __construct(private OrgUnitService $orgUnitService)
    {

    }

    public static function new(OrgUnitService $orgUnitService): OrgUnitsFacade
    {
        return new self($orgUnitService);
    }

    /**
     * @return OrgUnitResponse[]
     */
    public function get(): array
    {
        $courses = $this->orgUnitService->findAll();
        $courseResponses = [];

        foreach ($courses as $course) {
            $courseResponses[] = OrgUnitResponse::formDomain($course);
        }
        return $courseResponses;
    }
}