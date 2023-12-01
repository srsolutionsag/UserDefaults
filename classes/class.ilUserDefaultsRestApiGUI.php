<?php

use JetBrains\PhpStorm\NoReturn;
use srag\Plugins\UserDefaults\Adapters\Api;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;


/**
 * @ilCtrl_IsCalledBy ilUserDefaultsRestApiGUI: ilUserDefaultsConfigGUI
 */
class ilUserDefaultsRestApiGUI
{
    private Api\Course\CoursesApiEndpoint $coursesApiEndpoint;
    private Api\GlobalRole\GlobalRolesApiEndpoint $globalRolesApiEndpoint;
    private Api\LocalRole\LocalRolesApiEndpoint $localRolesApiEndpoint;
    private Api\Group\GroupsApiEndpoint $groupsApiEndpoint;
    private Api\OrgUnits\OrgUnitsApiEndpoint $orgUnitsApiEndpoint;
    private Api\PortfolioTemplate\PortfolioTemplatesApiEndpoint $portfolioTemplatesApiEndpoint;
    private Api\StudyProgramme\StudyProgrammesApiEndpoint $studyProgrammesApiEndpoint;


    /**
     * @return object{courses: string, globalRoles: string, groups: string, localRoles: string, orgUnits: string, orgUnitPositions: string, portfolioTemplates: string, studyProgrammes: string}
     */
    public static function commandNames(): object
    {
        return new class() {
            public string $courses = "courses";
            public string $globalRoles = "globalRoles";
            public string $groups = "groups";
            public string $localRoles = "localRoles";
            public string $orgUnits = "orgUnits";

            public string $orgUnitPositions = "orgUnitPositions";
            public string $portfolioTemplates = "portfolioTemplates";
            public string $studyProgrammes = "studyProgrammes";
        };
    }

    private ilCtrlInterface $ctrl;

    public function __construct()
    {
        global $DIC;
        $this->ctrl = $DIC->ctrl();

        $configs = Configs::new();
        $this->coursesApiEndpoint = Api\Course\CoursesApiEndpoint::new($configs);
        $this->globalRolesApiEndpoint = Api\GlobalRole\GlobalRolesApiEndpoint::new($configs);
        $this->localRolesApiEndpoint = Api\LocalRole\LocalRolesApiEndpoint::new($configs);
        $this->groupsApiEndpoint = Api\Group\GroupsApiEndpoint::new($configs);
        $this->orgUnitsApiEndpoint = Api\OrgUnits\OrgUnitsApiEndpoint::new($configs);
        $this->portfolioTemplatesApiEndpoint = Api\PortfolioTemplate\PortfolioTemplatesApiEndpoint::new($configs);
        $this->studyProgrammesApiEndpoint = Api\StudyProgramme\StudyProgrammesApiEndpoint::new($configs);

    }

    public function executeCommand(): void
    {
        $cmd = $this->ctrl->getCmd();
        match ($cmd) {
            $this->commandNames()->courses => $this->courses(),
            $this->commandNames()->globalRoles => $this->globalRoles(),
            $this->commandNames()->groups => $this->groups(),
            $this->commandNames()->localRoles => $this->localRoles(),
            $this->commandNames()->orgUnits => $this->orgUnits(),
            $this->commandNames()->orgUnitPositions => $this->orgUnitPositions(),
            $this->commandNames()->portfolioTemplates => $this->portfolioTemplates(),
            $this->commandNames()->studyProgrammes => $this->studyProgrammes(),
        };
    }

    #[NoReturn] public function courses(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->coursesApiEndpoint->get());
        exit;
    }

    #[NoReturn] public function globalRoles(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->globalRolesApiEndpoint->get());
        exit;
    }

    #[NoReturn] public function groups(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->groupsApiEndpoint->get());
        exit;
    }

    #[NoReturn] public function localRoles(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->localRolesApiEndpoint->get());
        exit;
    }

    #[NoReturn] public function orgUnits(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->orgUnitsApiEndpoint->get());
        exit;
    }

    #[NoReturn] public function orgUnitPositions(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->orgUnitsApiEndpoint->get());
        exit;
    }

    #[NoReturn] public function portfolioTemplates(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->portfolioTemplatesApiEndpoint->get());
        exit;
    }

    #[NoReturn] public function studyProgrammes(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->studyProgrammesApiEndpoint->get());
        exit;
    }
}