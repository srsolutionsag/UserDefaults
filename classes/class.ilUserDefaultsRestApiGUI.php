<?php

use srag\Plugins\UserDefaults\UserDefaultsApi;

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
        $cmd = $this->ctrl->getCmd();
        switch ($cmd) {
            case static::commandNames()->courses:
                $this->courses();
                break;
            case static::commandNames()->globalRoles:
                $this->globalRoles();
                break;
            case static::commandNames()->groups:
                $this->groups();
                break;
            case static::commandNames()->localRoles:
                $this->localRoles();
                break;
            case static::commandNames()->orgUnits:
                $this->orgUnits();
                break;
            case static::commandNames()->orgUnitPositions:
                $this->orgUnitPositions();
                break;
            case static::commandNames()->portfolioTemplates:
                $this->portfolioTemplates();
                break;
            case static::commandNames()->studyProgrammes:
                $this->studyProgrammes();
                break;
        }
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
