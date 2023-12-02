<?php

use JetBrains\PhpStorm\NoReturn;
use srag\Plugins\UserDefaults\UserDefaultsApi;


/**
 * @ilCtrl_IsCalledBy ilUserDefaultsRestApiGUI: ilUserDefaultsConfigGUI
 */
class ilUserDefaultsRestApiGUI
{
    private UserDefaultsApi $userDefaultsApi;


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
        //is Admin?
        if(in_array(2, $DIC->rbac()->review()->assignedGlobalRoles($DIC->user()->getId())) === false) {
            echo "no Permission";
            exit;
        };

        $this->userDefaultsApi =  UserDefaultsApi::new();
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
        echo json_encode($this->userDefaultsApi->courses->findAll());
        exit;
    }

    #[NoReturn] public function globalRoles(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->globalRoles->findAll());
        exit;
    }

    #[NoReturn] public function groups(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->groups->findAll());
        exit;
    }

    #[NoReturn] public function localRoles(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->localRoles->findAll());
        exit;
    }

    #[NoReturn] public function orgUnits(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->orgUnits->findAll());
        exit;
    }

    #[NoReturn] public function orgUnitPositions(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->orgUnits->findAllPositions());
        exit;
    }

    #[NoReturn] public function portfolioTemplates(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->portfolioTemplates->findAll());
        exit;
    }

    #[NoReturn] public function studyProgrammes(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->studyProgrammes->findAll());
        exit;
    }
}