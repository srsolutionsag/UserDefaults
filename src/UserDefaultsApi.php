<?php

namespace srag\Plugins\UserDefaults;

use srag\Plugins\UserDefaults\Adapters\Api;
use srag\Plugins\UserDefaults\Adapters\Config\Configs;

class UserDefaultsApi
{

    private function __construct(
        public Api\Course\Api            $courses,
        public Api\AssignmentProcess\Api $assignmentProcesses,
        public Api\Ui\Api                $uiComponents,
        public Api\GlobalRole\Api        $globalRoles,
        public Api\LocalRole\Api         $localRoles,
        public Api\Group\Api             $groups,
        public Api\OrgUnits\Api          $orgUnits,
        public Api\PortfolioTemplate\Api $portfolioTemplates,
        public Api\StudyProgramme\Api    $studyProgrammes
    )
    {

    }

    public static function new(): UserDefaultsApi
    {
        $configs = Configs::new();
        return new self(
            Api\Course\Api::new($configs),
            Api\AssignmentProcess\Api::new($configs),
            Api\Ui\Api::new($configs),
            Api\GlobalRole\Api::new($configs),
            Api\LocalRole\Api::new($configs),
            Api\Group\Api::new($configs),
            Api\OrgUnits\Api::new($configs),
            Api\PortfolioTemplate\Api::new($configs),
            Api\StudyProgramme\Api::new($configs)
        );
    }
}