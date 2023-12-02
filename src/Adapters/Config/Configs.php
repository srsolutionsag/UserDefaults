<?php

namespace srag\Plugins\UserDefaults\Adapters\Config;

use srag\Plugins\UserDefaults\Adapters;
use srag\Plugins\UserDefaults\Domain\Ports;

class Configs
{
    private function __construct(
        public Ports\CourseService            $courseService,
        public Ports\GlobalRoleService        $globalRoleService,
        public Ports\GroupService             $groupService,
        public Ports\LocalRoleService         $localRoleService,
        public Ports\OrgUnitService           $orgUnitService,
        public Ports\PortfolioTemplateService $portfolioTemplateService,
        public Ports\StudyProgrammeService    $studyProgrammeService,
        public Ports\AssignmentProcessService $assignmentProcessService,
        public string                         $adaptersWebPath,
        public \ILIAS\DI\UIServices           $iliasUiServices
    )
    {

    }

    public static function new(): Configs
    {
        global $DIC;
        return new self(
            Ports\CourseService::new(Adapters\Persistence\Course\IliasCourseRepository::new($DIC->database())),
            Ports\GlobalRoleService::new(Adapters\Persistence\GlobalRole\IliasGlobalRoleRepository::new($DIC->rbac()->review(), $DIC->repositoryTree())),
            Ports\GroupService::new(Adapters\Persistence\Group\IliasGroupRepository::new($DIC->database())),
            Ports\LocalRoleService::new(Adapters\Persistence\LocalRole\IliasLocalRoleRepository::new($DIC->rbac()->review(), $DIC->repositoryTree())),
            Ports\OrgUnitService::new(Adapters\Persistence\OrgUnit\IliasOrgUnitRepository::new($DIC->database())),
            Ports\PortfolioTemplateService::new(Adapters\Persistence\PortfolioTemplate\IliasPortfolioTemplateRepository::new($DIC->database())),
            Ports\StudyProgrammeService::new(Adapters\Persistence\StudyProgramme\IliasStudyProgrammeRepository::new($DIC->database())),
            Ports\AssignmentProcessService::new(Adapters\Persistence\StudyProgramme\IliasStudyProgrammeRepository::new($DIC->database())),
            \ilUserDefaultsPlugin::getInstance()->getDirectory() . "/src/Adapters",
            $DIC->ui()
        );
    }
}