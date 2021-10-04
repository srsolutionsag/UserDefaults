<?php

namespace srag\Plugins\UserDefaults\UserSetting;

use ilCheckboxInputGUI;
use ilFormSectionHeaderGUI;
use ilObjPortfolioTemplate;
use ilOrgUnitPosition;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use ilTemplateException;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\DIC\UserDefaults\Exception\DICException;
use srag\Plugins\UserDefaults\Access\Categories;
use srag\Plugins\UserDefaults\Access\Courses;
use srag\Plugins\UserDefaults\Access\GlobalRoles;
use srag\Plugins\UserDefaults\Access\LocalRoles;
use srag\Plugins\UserDefaults\Form\ilContainerMultiSelectInputGUI;
use srag\Plugins\UserDefaults\Form\SortableMultiSelectSearchInputGUI;
use srag\Plugins\UserDefaults\Form\udfMultiLineInputGUI;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UserSettingsGUI;

/**
 * Class ilUserSettingsFormGUI
 *
 * @package srag\Plugins\UserDefaults\UserSetting
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class UserSettingsFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const F_TITLE = 'title';
	const F_STATUS = 'status';
	const F_GLOBAL_ROLES = 'global_roles';
    const F_UNSIGN_GLOBAL_ROLES = 'unsubscribe_global_roles';
    const F_ASSIGNED_LOCAL_ROLES = 'assigned_local_roles';
    const F_UNSIGN_LOCAL_ROLES = 'unsubscribe_local_roles';
    const F_ASSIGNED_COURSES = 'assigned_courses';
	const F_UNSUBSCRIBE_COURSES_AND_CATEGORIES = 'unsubscribe_courses_and_categories';
    const F_UNSUBSCRIBE_GROUPS = 'unsubscribe_groups';
    const F_ASSIGNED_GROUPS = 'assigned_groups';
    const F_ASSIGNED_GROUPS_OPTION_REQUEST = 'assigned_groups_option_request';
    const F_ASSIGNED_GROUPS_QUEUE = 'assigned_groups_queue';
    const F_ASSIGNED_GROUPS_QUEUE_DESKTOP = 'assigned_groups_queue_desktop';
    const F_ASSIGNED_GROUPS_QUEUE_TYPE = 'assigned_groups_queue_type';
    const F_ASSIGNED_GROUPS_QUEUE_PARALLEL = 'assigned_groups_queue_parallel';
    const F_ASSIGNED_GROUPS_QUEUE_SERIAL = 'assigned_groups_queue_serial';
    const F_PORTFOLIO_TEMPLATE_ID = 'portfolio_template_id';
	const F_PORTFOLIO_ASSIGNED_TO_GROUPS = 'portfolio_assigned_to_groups';
	const F_ASSIGNED_ORGUS = 'assigned_orgus';
    const F_ASSIGNED_ORGU_POSITION = 'assigned_orgu_position';
    const F_UNSUBSCRIBE_ORGUS = 'unsubscribe_orgus';
    const F_ASSIGNED_STUDYPROGRAMS = 'assigned_studyprograms';
    const F_UNSUBSCRIBE_STUDYPROGRAMS = 'unsubscribe_studyprograms';
    const F_DESCRIPTION = 'description';
	const F_PORTFOLIO_NAME = 'portfolio_name';
    const F_REMOVE_PORTFOLIO = 'remove_portfolio';
    const F_BLOG_NAME = 'blog_name';
	const F_ON_CREATE = 'on_create';
	const F_ON_UPDATE = 'on_update';
	const F_ON_MANUAL = 'on_manual';
	const F_APPLICATION = 'application';
	/**
	 * @var UserSettingsGUI
	 */
	protected $parent_gui;
	/**
	 * @var UserSetting
	 */
	protected $object;

    /**
     * @var array
     */
	private $orguPositions;

	/**
	 * @param UserSettingsGUI $parent_gui
	 * @param UserSetting     $ilUserSetting
	 */
	public function __construct(UserSettingsGUI $parent_gui, UserSetting $ilUserSetting) {
		parent::__construct();
		$this->parent_gui = $parent_gui;
		$this->object = $ilUserSetting;
		//		self::plugin()->getPluginObject()->updateLanguageFiles();
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_gui));
		$this->initForm();
	}


    /**
     * @param $key
     *
     * @return string
     * @throws DICException
     */
	protected function txt($key) {
		return self::plugin()->translate($key, 'set');
	}


    /**
     * @throws ilTemplateException
     * @throws DICException
     */
	protected function initForm() {
		$this->setTitle(self::plugin()->translate('form_title'));
		$te = new ilTextInputGUI($this->txt(self::F_TITLE), self::F_TITLE);
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilTextAreaInputGUI($this->txt(self::F_DESCRIPTION), self::F_DESCRIPTION);
		$this->addItem($te);

		// $cb = new ilCheckboxInputGUI($this->txt(self::F_STATUS), self::F_STATUS);
		//		$this->addItem($cb);

		$a_item = new ilFormSectionHeaderGUI();
		$a_item->setTitle($this->txt(self::F_APPLICATION));
		$this->addItem($a_item);
		$this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_CREATE), self::F_ON_CREATE));
		$this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_UPDATE), self::F_ON_UPDATE));
		$this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_MANUAL), self::F_ON_MANUAL));

		// roles
		$a_item = new ilFormSectionHeaderGUI();
		$a_item->setTitle($this->txt('roles'));
		$this->addItem($a_item);

		$ilGlobalRoleMultiSelectInputGUI = new ilContainerMultiSelectInputGUI(GlobalRoles::TYPE_GLOBAL_ROLE, $this->txt(self::F_GLOBAL_ROLES), self::F_GLOBAL_ROLES);
        $ilGlobalRoleMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_GLOBAL_ROLES));
		$this->addItem($ilGlobalRoleMultiSelectInputGUI);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSIGN_GLOBAL_ROLES), self::F_UNSIGN_GLOBAL_ROLES);
        $this->addItem($ilCheckboxInputGUI);

		$ilLocalRoleMultiSelectInputGUI = new ilContainerMultiSelectInputGUI(LocalRoles::TYPE_LOCAL_ROLE, $this->txt(self::F_ASSIGNED_LOCAL_ROLES), self::F_ASSIGNED_LOCAL_ROLES);
		$ilLocalRoleMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_LOCAL_ROLES));
		$this->addItem($ilLocalRoleMultiSelectInputGUI);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSIGN_LOCAL_ROLES), self::F_UNSIGN_LOCAL_ROLES);
        $this->addItem($ilCheckboxInputGUI);

		// Assign Courses
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->txt('courses_and_categories'));
        $this->addItem($a_item);

        $multiSelect = new udfMultiLineInputGUI($this->txt(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS), "MultiGroup");
		$multiSelect->setShowLabel(true);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI(Courses::TYPE_CRS, $this->txt(self::F_ASSIGNED_COURSES), self::F_ASSIGNED_COURSES);
		$ilCourseMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES), self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES);
		$this->addItem($ilCheckboxInputGUI);

		// Assign to Groups
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->txt('groups'));
        $this->addItem($a_item);

        $ilGroupMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_ASSIGNED_GROUPS), self::F_ASSIGNED_GROUPS);
		$ilGroupMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilGroupMultiSelectInputGUI);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSUBSCRIBE_GROUPS), self::F_UNSUBSCRIBE_GROUPS);
        $this->addItem($ilCheckboxInputGUI);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_ASSIGNED_GROUPS_OPTION_REQUEST), self::F_ASSIGNED_GROUPS_OPTION_REQUEST);
        $this->addItem($ilCheckboxInputGUI);


        // groups queue
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->txt('groups_queue'));
        $this->addItem($a_item);

        $groups_queue_input = new SortableMultiSelectSearchInputGUI($this->txt(self::F_ASSIGNED_GROUPS_QUEUE), self::F_ASSIGNED_GROUPS_QUEUE);
        $groups_queue_input->setInfo($this->txt(self::F_ASSIGNED_GROUPS_QUEUE . '_info'));
        $groups_queue_input->setMulti(true, true);
        $groups_queue_input->setAllowEmptyFields(true);

        $ilGroupMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_ASSIGNED_GROUPS_QUEUE), 'obj_id', false, true, true);
        $ilGroupMultiSelectInputGUI->setWidth("600px");
        self::dic()->ctrl()->setParameter($this->parent_gui, 'with_parent', 1);
        self::dic()->ctrl()->setParameter($this->parent_gui, 'with_members', 1);
        self::dic()->ctrl()->setParameter($this->parent_gui, 'with_empty', 1);
        $ilGroupMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
        self::dic()->ctrl()->setParameter($this->parent_gui, 'with_parent', 0);
        self::dic()->ctrl()->setParameter($this->parent_gui, 'with_members', 0);
        self::dic()->ctrl()->setParameter($this->parent_gui, 'with_empty', 0);
        $ilGroupMultiSelectInputGUI->setLinkToObject(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_LINK_TO_OBJECT));
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

        // other
        $a_item = new ilFormSectionHeaderGUI();
        $a_item->setTitle($this->txt('other'));
        $this->addItem($a_item);

        $se = new ilSelectInputGUI($this->txt(self::F_PORTFOLIO_TEMPLATE_ID), self::F_PORTFOLIO_TEMPLATE_ID);

		$options = ilObjPortfolioTemplate::getAvailablePortfolioTemplates();
		//		$options[0] = self::plugin()->translate('crs_no_template');
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

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS), self::F_PORTFOLIO_ASSIGNED_TO_GROUPS);
		$ilCourseMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$ilOrgUnitMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('orgu', $this->txt(self::F_ASSIGNED_ORGUS), self::F_ASSIGNED_ORGUS);
		$ilOrgUnitMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilOrgUnitMultiSelectInputGUI);

		$selectOrguPosition = new ilSelectInputGUI($this->txt(self::F_ASSIGNED_ORGU_POSITION), self::F_ASSIGNED_ORGU_POSITION);
        $this->orguPositions = ilOrgUnitPosition::get();
		$optionPosis = array_map(function($pos) {
            return $pos->getId() . ": " . $pos->getTitle();
        }, $this->orguPositions);

		$selectOrguPosition->setOptions($optionPosis);
        if (!is_null($this->object->getAssignedOrguPosition())) {
            $selectOrguPosition->setDisabled(true);
        }
		$this->addItem($selectOrguPosition);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSUBSCRIBE_ORGUS), self::F_UNSUBSCRIBE_ORGUS);
        $this->addItem($ilCheckboxInputGUI);

		$ilStudyProgramMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('prg', $this->txt(self::F_ASSIGNED_STUDYPROGRAMS), self::F_ASSIGNED_STUDYPROGRAMS);
		$ilStudyProgramMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilStudyProgramMultiSelectInputGUI);

        $ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSUBSCRIBE_STUDYPROGRAMS), self::F_UNSUBSCRIBE_STUDYPROGRAMS);
        $this->addItem($ilCheckboxInputGUI);

		$this->addCommandButtons();
	}


	/**
	 * @param $existing_array
	 * @param $filter
	 *
	 * @return array
	 */
	protected static function appendRoles(array &$existing_array, $filter) {
		foreach (self::dic()->rbac()->review()->getRolesByFilter($filter) as $role) {

			if ($role['obj_id'] == 2) {
				continue;
			}
			$existing_array[$role['obj_id']] = $role[self::F_TITLE] . ' (' . $role['obj_id'] . ')';
			$opt[$role['obj_id']] = $role[self::F_TITLE] . ' (' . $role['obj_id'] . ')';
			$role_ids[] = $role['obj_id'];
		}

		return $existing_array;
	}


	public function fillForm() {
	    $assigned_groups_queue = array_map(function($e){return ['obj_id' => $e];},$this->object->getAssignedGroupsQueue());
	    $assigned_groups_queue = array_values($assigned_groups_queue);
        $assignedOrguPosition = $this->object->getAssignedOrguPosition();
        $selectOrguPosVal = current(array_filter($this->orguPositions, function ($pos) use($assignedOrguPosition) { return $pos->getId() == $assignedOrguPosition; }));
        $array = array(
            self::F_TITLE                              => $this->object->getTitle(),
            self::F_DESCRIPTION                        => $this->object->getDescription(),
            //			self::F_STATUS => ($this->object->getStatus() == ilUserSetting::STATUS_ACTIVE ? 1 : 0),
            self::F_ASSIGNED_LOCAL_ROLES               => implode(',', $this->object->getAssignedLocalRoles()),
            self::F_UNSIGN_LOCAL_ROLES                 => $this->object->isUnsignLocalRoles(),
            self::F_ASSIGNED_COURSES                   => implode(',', $this->object->getAssignedCourses()),
            self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES => $this->object->isUnsubscrfromcrsAndcategoriesDesktop(),
            self::F_ASSIGNED_GROUPS                    => implode(',', $this->object->getAssignedGroupes()),
            self::F_UNSUBSCRIBE_GROUPS                 => $this->object->isUnsubscrfromgrp(),
            self::F_ASSIGNED_GROUPS_OPTION_REQUEST     => $this->object->isAssignedGroupsOptionRequest(),
            self::F_ASSIGNED_GROUPS_QUEUE              => $assigned_groups_queue,
            self::F_ASSIGNED_GROUPS_QUEUE_DESKTOP      => $this->object->isGroupsQueueDesktop(),
            self::F_ASSIGNED_GROUPS_QUEUE_TYPE         => $this->object->isGroupsQueueParallel(),
            self::F_GLOBAL_ROLES                       => $this->object->getGlobalRoles(),
            self::F_UNSIGN_GLOBAL_ROLES                => $this->object->isUnsignGlobalRoles(),
            self::F_PORTFOLIO_TEMPLATE_ID              => $this->object->getPortfolioTemplateId(),
            self::F_PORTFOLIO_ASSIGNED_TO_GROUPS       => implode(',', $this->object->getPortfolioAssignedToGroups()),
            self::F_BLOG_NAME                          => $this->object->getBlogName(),
            self::F_PORTFOLIO_NAME                     => $this->object->getPortfolioName(),
            self::F_REMOVE_PORTFOLIO                   => $this->object->getRemovePortfolio(),
            self::F_ASSIGNED_ORGUS                     => implode(',', $this->object->getAssignedOrgus()),
            self::F_ASSIGNED_ORGU_POSITION             => gettype($selectOrguPosVal) === "object" ? $selectOrguPosVal->getId() : false,
            self::F_UNSUBSCRIBE_ORGUS                  => $this->object->isUnsubscrFromOrgus(),
            self::F_ASSIGNED_STUDYPROGRAMS             => implode(',', $this->object->getAssignedStudyprograms()),
            self::F_UNSUBSCRIBE_STUDYPROGRAMS          => $this->object->isUnsubscrFromStudyprograms(),
            self::F_ON_CREATE                          => $this->object->isOnCreate(),
            self::F_ON_UPDATE                          => $this->object->isOnUpdate(),
            self::F_ON_MANUAL                          => $this->object->isOnManual(),
        );
        $this->setValuesByArray($array);
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (!$this->checkInput()) {
			return false;
		}
		$this->object->setTitle($this->getInput(self::F_TITLE));
		$this->object->setDescription($this->getInput(self::F_DESCRIPTION));

		$assigned_local_roles = $this->getInput(self::F_ASSIGNED_LOCAL_ROLES);
		$this->object->setAssignedLocalRoles(explode(',', $assigned_local_roles[0]));
		$this->object->setUnsignLocalRoles($this->getInput(self::F_UNSIGN_LOCAL_ROLES));

		$assigned_courses = $this->getInput(self::F_ASSIGNED_COURSES);
		$this->object->setAssignedCourses(explode(',', $assigned_courses[0]));

		$this->object->setUnsubscrfromcrsAndcategoriesDesktop($this->getInput(self::F_UNSUBSCRIBE_COURSES_AND_CATEGORIES));
		$assigned_groups = $this->getInput(self::F_ASSIGNED_GROUPS);
		$this->object->setAssignedGroupes(explode(',', $assigned_groups[0]));
		$this->object->setUnsubscrfromgrpDesktop($this->getInput(self::F_UNSUBSCRIBE_GROUPS));
		$this->object->setAssignedGroupsOptionRequest($this->getInput(self::F_ASSIGNED_GROUPS_OPTION_REQUEST));
		$assigned_groups_option_request = $this->getInput(self::F_ASSIGNED_GROUPS_OPTION_REQUEST);

		$this->object->setGlobalRoles(explode(',', $this->getInput(self::F_GLOBAL_ROLES)[0]));
        $this->object->setUnsignGlobalRoles($this->getInput(self::F_UNSIGN_GLOBAL_ROLES));

        $portfolio_template_id = $this->getInput(self::F_PORTFOLIO_TEMPLATE_ID);
		$this->object->setPortfolioTemplateId($portfolio_template_id > 0 ? $portfolio_template_id : NULL);
		$portf_assigned_to_groups = $this->getInput(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS);
		$this->object->setPortfolioAssignedToGroups(explode(',', $portf_assigned_to_groups[0]));
		$this->object->setBlogName($this->getInput(self::F_BLOG_NAME));
		$this->object->setPortfolioName($this->getInput(self::F_PORTFOLIO_NAME));
        $this->object->setRemovePortfolio($this->getInput(self::F_REMOVE_PORTFOLIO));

        $assigned_orgus = $this->getInput(self::F_ASSIGNED_ORGUS);
        $this->object->setAssignedOrgus(explode(',', $assigned_orgus[0]));

        $orguPosSelectVal = (int)$this->getInput(self::F_ASSIGNED_ORGU_POSITION);
        $id = substr($orguPosSelectVal, 0, strpos($orguPosSelectVal, ":"));
        $this->object->setAssignedOrguPosition($orguPosSelectVal);
        $this->object->setUnsubscrFromOrgus((int)$this->getInput(self::F_ASSIGNED_ORGU_POSITION));

        $assigned_studyprograms = $this->getInput(self::F_ASSIGNED_STUDYPROGRAMS);
		$this->object->setAssignedStudyprograms(explode(',', $assigned_studyprograms[0]));
        $this->object->setUnsubscrFromstudyprograms($this->getInput(self::F_UNSUBSCRIBE_STUDYPROGRAMS));

		$this->object->setAssignedGroupsQueue(array_filter(array_values(array_map(function ($element) {
		    return $element['obj_id'];
        }, $this->getInput(self::F_ASSIGNED_GROUPS_QUEUE)))));

		$this->object->setGroupsQueueDesktop((bool)$this->getInput(self::F_ASSIGNED_GROUPS_QUEUE_DESKTOP));
		$this->object->setGroupsQueueParallel((bool)$this->getInput(self::F_ASSIGNED_GROUPS_QUEUE_TYPE));

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


	protected function addCommandButtons() {
		if ($this->object->getId() > 0) {
			$this->addCommandButton(UserSettingsGUI::CMD_UPDATE, self::plugin()->translate('form_button_update'));
		} else {
			$this->addCommandButton(UserSettingsGUI::CMD_CREATE, self::plugin()->translate('form_button_create'));
		}
		$this->addCommandButton(UserSettingsGUI::CMD_CANCEL, self::plugin()->translate('form_button_cancel'));
	}
}
