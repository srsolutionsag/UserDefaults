<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Responses;

use ilCheckboxInputGUI;
use ilFormSectionHeaderGUI;
use ilObjPortfolioTemplate;
use ilOrgUnitLocalDIC;
use ilPropertyFormGUI;
use ilSelectInputGUI;
use ilTemplateException;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use ilUserDefaultsConfigGUI;
use ilUserDefaultsPlugin;
use ilUserDefaultsRestApiGUI;
use srag\Plugins\UserDefaults\Form\udfMultiLineInputGUI;
use srag\Plugins\UserDefaults\UserDefaultsApi;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UserSettingsGUI;

class Form extends ilPropertyFormGUI
{
    use UserDefaultsTrait;

    public $positionRepo;

    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    public const F_TITLE = 'title';
    public const F_STATUS = 'status';
    public const F_GLOBAL_ROLES = 'global_roles';
    public const F_UNSIGN_GLOBAL_ROLES = 'unsubscribe_global_roles';
    public const F_ASSIGNED_LOCAL_ROLES = 'assigned_local_roles';
    public const F_UNSIGN_LOCAL_ROLES = 'unsubscribe_local_roles';
    public const F_ASSIGNED_COURSES = 'assigned_courses';
    public const F_UNSUBSCRIBE_COURSES_AND_CATEGORIES = 'unsubscribe_courses_and_categories';
    public const F_UNSUBSCRIBE_GROUPS = 'unsubscribe_groups';
    public const F_ASSIGNED_GROUPS = 'assigned_groups';
    public const F_ASSIGNED_GROUPS_OPTION_REQUEST = 'assigned_groups_option_request';
    public const F_ASSIGNED_GROUPS_QUEUE = 'assigned_groups_queue';
    public const F_ASSIGNED_GROUPS_QUEUE_DESKTOP = 'assigned_groups_queue_desktop';
    public const F_ASSIGNED_GROUPS_QUEUE_TYPE = 'assigned_groups_queue_type';
    public const F_ASSIGNED_GROUPS_QUEUE_PARALLEL = 'assigned_groups_queue_parallel';
    public const F_ASSIGNED_GROUPS_QUEUE_SERIAL = 'assigned_groups_queue_serial';
    public const F_PORTFOLIO_TEMPLATE_ID = 'portfolio_template_id';
    public const F_PORTFOLIO_ASSIGNED_TO_GROUPS = 'portfolio_assigned_to_groups';
    public const F_ASSIGNED_ORGUS = 'assigned_orgus';
    public const F_ASSIGNED_ORGU_POSITION = 'assigned_orgu_position';
    public const F_UNSUBSCRIBE_ORGUS = 'unsubscribe_orgus';
    public const F_ASSIGNED_STUDYPROGRAMS = 'assigned_studyprograms';
    public const F_UNSUBSCRIBE_STUDYPROGRAMS = 'unsubscribe_studyprograms';
    public const F_DESCRIPTION = 'description';
    public const F_PORTFOLIO_NAME = 'portfolio_name';
    public const F_REMOVE_PORTFOLIO = 'remove_portfolio';
    public const F_BLOG_NAME = 'blog_name';
    public const F_ON_CREATE = 'on_create';
    public const F_ON_UPDATE = 'on_update';
    public const F_ON_MANUAL = 'on_manual';
    public const F_APPLICATION = 'application';
    private array $orguPositions;
    private ilUserDefaultsPlugin $pl;
    private UserDefaultsApi $userDefaultsApi;

    /**
     * @param UserSettingsGUI $parent_gui
     * @param UserSetting     $object
     * @throws \ilCtrlException
     */
    public function __construct(protected UserSettingsGUI $parent_gui, protected UserSetting $object)
    {
        global $DIC;
        $orgus = ilOrgUnitLocalDIC::dic();
        $this->positionRepo = $orgus["repo.Positions"];
        parent::__construct();
        $this->pl = ilUserDefaultsPlugin::getInstance();
        $this->ctrl = $DIC->ctrl();

        $this->userDefaultsApi = UserDefaultsApi::new();

        $this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
        $this->initForm();
    }

    public static function new(object $parentIliasGui, ?int $assignmentProcessId): self
    {
        //todo
        if ($assignmentProcessId === null) {
            $assignmentProcessId = 0;
        }

        $form = new self($parentIliasGui, new UserSetting($assignmentProcessId));

        if ($assignmentProcessId > 0) {
            $form->fillForm();
        }
        return $form;
    }

    protected function txt(string $key): string
    {
        return $this->pl->txt('set_' . $key);
    }

    /**
     * @throws \ilCtrlException
     * @throws ilTemplateException
     */
    protected function initForm(): void
    {
        $this->setTitle($this->pl->txt('form_title'));
        $te = new ilTextInputGUI($this->txt(self::F_TITLE), self::F_TITLE);
        $te->setRequired(true);
        $this->addItem($te);

        $te = new ilTextAreaInputGUI($this->txt(self::F_DESCRIPTION), self::F_DESCRIPTION);
        $this->addItem($te);

        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->txt(self::F_APPLICATION));
        $this->addItem($a_item);
        $this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_CREATE), self::F_ON_CREATE));
        $this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_UPDATE), self::F_ON_UPDATE));
        $this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_MANUAL), self::F_ON_MANUAL));

        // roles
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->pl->txt('global_roles'));
        $this->addItem($a_item);

        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_GLOBAL_ROLES),
                self::F_GLOBAL_ROLES,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->globalRoles
                )
            )
        );

        $ilCheckboxInputGUI = new ilCheckboxInputGUI(
            $this->txt(self::F_UNSIGN_GLOBAL_ROLES),
            self::F_UNSIGN_GLOBAL_ROLES
        );
        $this->addItem($ilCheckboxInputGUI);

        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->pl->txt('local_roles'));
        $this->addItem($a_item);

        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_ASSIGNED_LOCAL_ROLES),
                self::F_ASSIGNED_LOCAL_ROLES,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->localRoles
                )
            )
        );

        $ilCheckboxInputGUI = new ilCheckboxInputGUI(
            $this->txt(self::F_UNSIGN_LOCAL_ROLES),
            self::F_UNSIGN_LOCAL_ROLES
        );
        $this->addItem($ilCheckboxInputGUI);

        // Assign Courses
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->pl->txt('courses'));
        $this->addItem($a_item);

        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_ASSIGNED_COURSES),
                self::F_ASSIGNED_COURSES,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->courses
                )
            )
        );

        $ilCheckboxInputGUI = new ilCheckboxInputGUI(
            $this->txt(self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES),
            self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES
        );
        $this->addItem($ilCheckboxInputGUI);

        // Assign to Groups
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->txt('groups'));
        $this->addItem($a_item);

        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_ASSIGNED_GROUPS),
                self::F_ASSIGNED_GROUPS,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->groups
                )
            )
        );
        $ilCheckboxInputGUI = new ilCheckboxInputGUI(
            $this->txt(self::F_ASSIGNED_GROUPS_OPTION_REQUEST),
            self::F_ASSIGNED_GROUPS_OPTION_REQUEST
        );
        $this->addItem($ilCheckboxInputGUI);
        $ilCheckboxInputGUI = new ilCheckboxInputGUI(
            $this->txt(self::F_UNSUBSCRIBE_GROUPS),
            self::F_UNSUBSCRIBE_GROUPS
        );
        $this->addItem($ilCheckboxInputGUI);

        // groups queue
        /*
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->txt('groups_queue'));
        $this->addItem($a_item);

        $groups_queue_input = new SortableMultiSelectSearchInputGUI($this->txt(self::F_ASSIGNED_GROUPS_QUEUE), self::F_ASSIGNED_GROUPS_QUEUE);
        $groups_queue_input->setInfo($this->txt(self::F_ASSIGNED_GROUPS_QUEUE . '_info'));
        $groups_queue_input->setMulti(true, true);
        $groups_queue_input->setAllowEmptyFields(true);

        $ilGroupMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_ASSIGNED_GROUPS_QUEUE), 'obj_id', false, true, true);
        $ilGroupMultiSelectInputGUI->setWidth(600);
        $this->ctrl->setParameter($this->parent_gui, 'with_parent', 1);
        $this->ctrl->setParameter($this->parent_gui, 'with_members', 1);
        $this->ctrl->setParameter($this->parent_gui, 'with_empty', 1);
        $ilGroupMultiSelectInputGUI->setAjaxLink($this->ctrl->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
        $this->ctrl->setParameter($this->parent_gui, 'with_parent', 0);
        $this->ctrl->setParameter($this->parent_gui, 'with_members', 0);
        $this->ctrl->setParameter($this->parent_gui, 'with_empty', 0);
        $ilGroupMultiSelectInputGUI->setLinkToObject($this->ctrl->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_LINK_TO_OBJECT));
        $groups_queue_input->addInput($ilGroupMultiSelectInputGUI);

        $this->addItem($groups_queue_input);

        $queue_desktop = new ilCheckboxInputGUI($this->txt(self::F_ASSIGNED_GROUPS_QUEUE_DESKTOP), self::F_ASSIGNED_GROUPS_QUEUE_DESKTOP);
        $this->addItem($queue_desktop);

        $queue_parallel = new ilRadioGroupInputGUI($this->txt(self::F_ASSIGNED_GROUPS_QUEUE_TYPE), self::F_ASSIGNED_GROUPS_QUEUE_TYPE);

        $serial = new ilRadioOption($this->txt(self::F_ASSIGNED_GROUPS_QUEUE_SERIAL), 0);
        $serial->setInfo($this->txt(self::F_ASSIGNED_GROUPS_QUEUE_SERIAL . '_info'));
        $queue_parallel->addOption($serial);

        $parallel = new ilRadioOption($this->txt(self::F_ASSIGNED_GROUPS_QUEUE_PARALLEL), 1);
        $parallel->setInfo($this->txt(self::F_ASSIGNED_GROUPS_QUEUE_PARALLEL . '_info'));
        $queue_parallel->addOption($parallel);
        $this->addItem($queue_parallel);
        */

        //orgus
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->pl->txt('orgus'));
        $this->addItem($a_item);

        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_ASSIGNED_ORGUS),
                self::F_ASSIGNED_ORGUS,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->orgUnits
                )
            )
        );

        //todo
        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_ASSIGNED_ORGU_POSITION),
                self::F_ASSIGNED_ORGU_POSITION,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->orgUnitPositions
                )
            )
        );

        //$selectOrguPosition->setOptions($optionPosis);
        /*if (!is_null($this->object->getAssignedOrguPosition())) {
            $selectOrguPosition->setDisabled(true);
        }*/
        //$this->addItem($selectOrguPosition);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSUBSCRIBE_ORGUS), self::F_UNSUBSCRIBE_ORGUS);
        $this->addItem($ilCheckboxInputGUI);

        //study programs
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->pl->txt('studyprograms'));
        $this->addItem($a_item);
        //todo
        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_ASSIGNED_STUDYPROGRAMS),
                self::F_ASSIGNED_STUDYPROGRAMS,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->studyProgrammes
                )
            )
        );

        $ilCheckboxInputGUI = new ilCheckboxInputGUI(
            $this->txt(self::F_UNSUBSCRIBE_STUDYPROGRAMS),
            self::F_UNSUBSCRIBE_STUDYPROGRAMS
        );
        $this->addItem($ilCheckboxInputGUI);

        /*
        $selectOrguPosition = new ilSelectInputGUI($this->txt(self::F_ASSIGNED_ORGU_POSITION), self::F_ASSIGNED_ORGU_POSITION);
        $this->orguPositions = ilOrgUnitPosition::get();
        $optionPosis = array_map(function ($pos) {
            return $pos->getId() . ": " . $pos->getTitle();
        }, $this->orguPositions);
        */

        //portfolio
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->pl->txt('portfolio'));
        $this->addItem($a_item);

        $se = new ilSelectInputGUI($this->txt(self::F_PORTFOLIO_TEMPLATE_ID), self::F_PORTFOLIO_TEMPLATE_ID);

        $options = ilObjPortfolioTemplate::getAvailablePortfolioTemplates();
        $options[1] = '--';

        asort($options);

        $se->setOptions($options);
        $this->addItem($se);

        $te = new ilTextInputGUI($this->txt(self::F_PORTFOLIO_NAME), self::F_PORTFOLIO_NAME);
        $te->setInfo(UserSetting::getAvailablePlaceholdersAsString());
        //		$te->setRequired(true);
        $this->addItem($te);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_REMOVE_PORTFOLIO), self::F_REMOVE_PORTFOLIO);
        $this->addItem($ilCheckboxInputGUI);

        $te = new ilTextInputGUI($this->txt(self::F_BLOG_NAME), self::F_BLOG_NAME);
        $this->addItem($te);

        $this->addItem(
            $this->userDefaultsApi->uiComponents->searchInputElementHtml(
                $this->txt(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS),
                self::F_PORTFOLIO_ASSIGNED_TO_GROUPS,
                $this->ctrl->getLinkTargetByClass(
                    [ilUserDefaultsConfigGUI::class, ilUserDefaultsRestApiGUI::class],
                    ilUserDefaultsRestApiGUI::commandNames()->portfolioTemplates
                )
            )
        );

        $this->addCommandButtons();
    }

    public function fillForm(): void
    {
        $assigned_groups_queue = array_map(fn($e): array => ['obj_id' => $e], $this->object->getAssignedGroupsQueue());
        $assigned_groups_queue = array_values($assigned_groups_queue);
        $assignedOrguPosition = $this->object->getAssignedOrguPosition();
        if ($assignedOrguPosition == null) {
            $assignedOrguPosition = 0;
        }
        $selectOrguPosVal = current(
            array_filter(
                $this->positionRepo->getAllPositions(),
                fn($pos): bool => $pos->getId() == $assignedOrguPosition
            )
        );

        $array = [
            self::F_TITLE => $this->object->getTitle(),
            self::F_DESCRIPTION => $this->object->getDescription(),
            //			self::F_STATUS => ($this->object->getStatus() == ilUserSetting::STATUS_ACTIVE ? 1 : 0),
            self::F_ASSIGNED_LOCAL_ROLES => $this->object->getAssignedLocalRoles(),
            self::F_UNSIGN_LOCAL_ROLES => $this->object->isUnsignLocalRoles(),
            self::F_ASSIGNED_COURSES => $this->object->getAssignedCourses(),
            self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES => $this->object->isUnsubscrfromcrsAndcategoriesDesktop(),
            self::F_ASSIGNED_GROUPS => $this->object->getAssignedGroupes(),
            self::F_UNSUBSCRIBE_GROUPS => $this->object->isUnsubscrfromgrp(),
            self::F_ASSIGNED_GROUPS_OPTION_REQUEST => $this->object->isAssignedGroupsOptionRequest(),
            self::F_ASSIGNED_GROUPS_QUEUE => $assigned_groups_queue,
            self::F_ASSIGNED_GROUPS_QUEUE_DESKTOP => $this->object->isGroupsQueueDesktop(),
            self::F_ASSIGNED_GROUPS_QUEUE_TYPE => $this->object->isGroupsQueueParallel(),
            self::F_GLOBAL_ROLES => $this->object->getGlobalRoles(),
            self::F_UNSIGN_GLOBAL_ROLES => $this->object->isUnsignGlobalRoles(),
            self::F_PORTFOLIO_TEMPLATE_ID => $this->object->getPortfolioTemplateId(),
            self::F_PORTFOLIO_ASSIGNED_TO_GROUPS => implode(',', $this->object->getPortfolioAssignedToGroups()),
            self::F_BLOG_NAME => $this->object->getBlogName(),
            self::F_PORTFOLIO_NAME => $this->object->getPortfolioName(),
            self::F_REMOVE_PORTFOLIO => $this->object->getRemovePortfolio(),
            self::F_ASSIGNED_ORGUS => $this->object->getAssignedOrgus(),
            self::F_ASSIGNED_ORGU_POSITION => (array) $this->object->getAssignedOrguPosition(),
            self::F_UNSUBSCRIBE_ORGUS => $this->object->isUnsubscrFromOrgus(),
            self::F_ASSIGNED_STUDYPROGRAMS => $this->object->getAssignedStudyprograms(),
            self::F_UNSUBSCRIBE_STUDYPROGRAMS => $this->object->isUnsubscrFromStudyprograms(),
            self::F_ON_CREATE => $this->object->isOnCreate(),
            self::F_ON_UPDATE => $this->object->isOnUpdate(),
            self::F_ON_MANUAL => $this->object->isOnManual(),
        ];

        $this->setValuesByArray($array);
    }

    public function saveObject(): bool
    {
        if (!$this->checkInput()) {
            return false;
        }
        $this->object->setTitle($this->getInput(self::F_TITLE));
        $this->object->setDescription($this->getInput(self::F_DESCRIPTION));

        $this->object->setGlobalRoles($this->getInput(self::F_GLOBAL_ROLES));
        $this->object->setUnsignGlobalRoles($this->getInput(self::F_UNSIGN_GLOBAL_ROLES));

        $this->object->setAssignedLocalRoles($this->getInput(self::F_ASSIGNED_LOCAL_ROLES));
        $this->object->setUnsignLocalRoles($this->getInput(self::F_UNSIGN_LOCAL_ROLES));

        $this->object->setAssignedCourses($this->getInput(self::F_ASSIGNED_COURSES));

        $this->object->setUnsubscrfromcrsAndcategoriesDesktop(
            $this->getInput(self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES)
        );
        $this->object->setAssignedGroupes($this->getInput(self::F_ASSIGNED_GROUPS));
        $this->object->setUnsubscrfromgrpDesktop($this->getInput(self::F_UNSUBSCRIBE_GROUPS));
        $this->object->setAssignedGroupsOptionRequest($this->getInput(self::F_ASSIGNED_GROUPS_OPTION_REQUEST));

        //$assigned_groups_option_request = $this->getInput(self::F_ASSIGNED_GROUPS_OPTION_REQUEST);

        //todo ?
        $this->object->setPortfolioTemplateId(
            $this->getInput(self::F_PORTFOLIO_TEMPLATE_ID) > 0 ? $this->getInput(self::F_PORTFOLIO_TEMPLATE_ID) : null
        );

        $this->object->setPortfolioAssignedToGroups($this->getInput(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS));
        $this->object->setBlogName($this->getInput(self::F_BLOG_NAME));
        $this->object->setPortfolioName($this->getInput(self::F_PORTFOLIO_NAME));
        $this->object->setRemovePortfolio($this->getInput(self::F_REMOVE_PORTFOLIO));

        $assigned_orgus = $this->getInput(self::F_ASSIGNED_ORGUS);
        $this->object->setAssignedOrgus($assigned_orgus);
        if (array_key_exists("0", $this->getInput(self::F_ASSIGNED_ORGU_POSITION))) {
            $this->object->setAssignedOrguPosition($this->getInput(self::F_ASSIGNED_ORGU_POSITION)[0]);
        }
        $this->object->setUnsubscrFromOrgus((int) $this->getInput(self::F_UNSUBSCRIBE_ORGUS));

        $assigned_studyprograms = $this->getInput(self::F_ASSIGNED_STUDYPROGRAMS);
        $this->object->setAssignedStudyprograms($assigned_studyprograms);
        $this->object->setUnsubscrFromstudyprograms($this->getInput(self::F_UNSUBSCRIBE_STUDYPROGRAMS));

        /*$this->object->setAssignedGroupsQueue(array_filter(array_values(array_map(function ($element) {
            return $element['obj_id'];
        }, $this->getInput(self::F_ASSIGNED_GROUPS_QUEUE)))));

        $this->object->setGroupsQueueDesktop((bool)$this->getInput(self::F_ASSIGNED_GROUPS_QUEUE_DESKTOP));
        $this->object->setGroupsQueueParallel((bool)$this->getInput(self::F_ASSIGNED_GROUPS_QUEUE_TYPE));
        */

        $this->object->setOnCreate($this->getInput(self::F_ON_CREATE));
        $this->object->setOnUpdate($this->getInput(self::F_ON_UPDATE));
        $this->object->setOnManual($this->getInput(self::F_ON_MANUAL));

        if ($this->object->getId() > 0) {
            $this->object->update();
        } else {
            $this->object->create();
        }

        return true;
    }

    protected function addCommandButtons(): void
    {
        if ($this->object->getId() > 0) {
            $this->addCommandButton(UserSettingsGUI::CMD_UPDATE, $this->pl->txt('form_button_update'));
        } else {
            $this->addCommandButton(UserSettingsGUI::CMD_CREATE, $this->pl->txt('form_button_create'));
        }
        $this->addCommandButton(UserSettingsGUI::CMD_CANCEL, $this->pl->txt('form_button_cancel'));
    }
}
