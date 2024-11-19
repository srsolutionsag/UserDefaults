<?php

use srag\Plugins\UserDefaults\API\UserDefaultsApi;
use srag\Plugins\UserDefaults\API\Commands;

/**
 * @ilCtrl_IsCalledBy ilUserDefaultsRestApiGUI: ilUserDefaultsConfigGUI
 */
class ilUserDefaultsRestApiGUI
{
    /**
     * @readonly
     */
    private UserDefaultsApi $userDefaultsApi;

    /**
     * @return object{courses: string, globalRoles: string, groups: string, localRoles: string, orgUnits: string, orgUnitPositions: string, portfolioTemplates: string, studyProgrammes: string}
     */
    public static function commandNames(): object
    {
        return new class () {
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

    /**
     * @readonly
     */
    private ilCtrlInterface $ctrl;

    public function __construct()
    {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        // fix DH: Has permission
        if (!ilUserDefaultsPlugin::grantAccess()) {
            echo "no Permission";
            exit;
        };

        $this->userDefaultsApi = UserDefaultsApi::new();
    }

    public function executeCommand(): void
    {
        $cmd = Commands::from($this->ctrl->getCmd());

        match ($cmd) {
            Commands::courses => $this->courses(),
            Commands::globalRoles => $this->globalRoles(),
            Commands::groups => $this->groups(),
            Commands::localRoles => $this->localRoles(),
            Commands::orgUnits => $this->orgUnits(),
            Commands::orgUnitPositions => $this->orgUnitPositions(),
            Commands::portfolioTemplates => $this->portfolioTemplates(),
            Commands::studyProgrammes => $this->studyProgrammes(),
            default => null
        };
    }

    public function courses(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->courses->findAll());
        exit;
    }

    public function globalRoles(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->globalRoles->findAll());
        exit;
    }

    public function groups(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->groups->findAll());
        exit;
    }

    public function localRoles(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->localRoles->findAll());
        exit;
    }

    public function orgUnits(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->orgUnits->findAll());
        exit;
    }

    public function orgUnitPositions(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->orgUnits->findAllPositions());
        exit;
    }

    public function portfolioTemplates(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->portfolioTemplates->findAll());
        exit;
    }

    public function studyProgrammes(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($this->userDefaultsApi->studyProgrammes->findAll());
        exit;
    }
}
