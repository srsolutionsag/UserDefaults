<?php

namespace srag\Plugins\UserDefaults\Adapters\Config;

use srag\Plugins\UserDefaults\Domain\Ports\CourseService;
use srag\Plugins\UserDefaults\Domain\Ports\GlobalRoleService;
use srag\Plugins\UserDefaults\Domain\Ports\GroupService;
use srag\Plugins\UserDefaults\Domain\Ports\LocalRoleService;
use srag\Plugins\UserDefaults\Domain\Ports\OrgUnitService;
use srag\Plugins\UserDefaults\Domain\Ports\PortfolioTemplateService;
use srag\Plugins\UserDefaults\Domain\Ports\StudyProgrammeService;
use srag\Plugins\UserDefaults\Domain\Ports\AssignmentProcessService;
use ILIAS\DI\UIServices;
use srag\Plugins\UserDefaults\Adapters\Persistence\Course\IliasCourseRepository;
use srag\Plugins\UserDefaults\Adapters\Persistence\GlobalRole\IliasGlobalRoleRepository;
use srag\Plugins\UserDefaults\Adapters\Persistence\Group\IliasGroupRepository;
use srag\Plugins\UserDefaults\Adapters\Persistence\LocalRole\IliasLocalRoleRepository;
use srag\Plugins\UserDefaults\Adapters\Persistence\OrgUnit\IliasOrgUnitRepository;
use srag\Plugins\UserDefaults\Adapters\Persistence\PortfolioTemplate\IliasPortfolioTemplateRepository;
use srag\Plugins\UserDefaults\Adapters\Persistence\StudyProgramme\IliasStudyProgrammeRepository;

class Configs
{
    private function __construct(
        public CourseService $courseService,
        public GlobalRoleService $globalRoleService,
        public GroupService $groupService,
        public LocalRoleService $localRoleService,
        public OrgUnitService $orgUnitService,
        public PortfolioTemplateService $portfolioTemplateService,
        public StudyProgrammeService $studyProgrammeService,
        public AssignmentProcessService $assignmentProcessService,
        public string $adaptersWebPath,
        public UIServices $iliasUiServices
    ) {
    }

    public static function new(): Configs
    {
        global $DIC;
        return new self(
            CourseService::new(IliasCourseRepository::new($DIC->database())),
            GlobalRoleService::new(IliasGlobalRoleRepository::new($DIC->rbac()->review(), $DIC->repositoryTree())),
            GroupService::new(IliasGroupRepository::new($DIC->database())),
            LocalRoleService::new(IliasLocalRoleRepository::new($DIC->rbac()->review(), $DIC->repositoryTree())),
            OrgUnitService::new(IliasOrgUnitRepository::new($DIC->database())),
            PortfolioTemplateService::new(IliasPortfolioTemplateRepository::new($DIC->database())),
            StudyProgrammeService::new(IliasStudyProgrammeRepository::new($DIC->database())),
            AssignmentProcessService::new(IliasStudyProgrammeRepository::new($DIC->database())),
            \ilUserDefaultsPlugin::getInstance()->getDirectory() . "/src/Adapters",
            $DIC->ui()
        );
    }
}
