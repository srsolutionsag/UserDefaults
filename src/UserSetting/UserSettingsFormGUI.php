<?php

namespace srag\Plugins\UserDefaults\UserSetting;
use ilCheckboxInputGUI;
use ilFormSectionHeaderGUI;
use ilObjPortfolioTemplate;
use ilPropertyFormGUI;
use ilRbacReview;
use ilSelectInputGUI;
use ilTextAreaInputGUI;
use ilTextInputGUI;
use ilUserDefaultsPlugin;
use UserSettingsGUI;
use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Form\ilContainerMultiSelectInputGUI;
use srag\Plugins\UserDefaults\Form\udfMultiLineInputGUI;

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
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const F_TITLE = 'title';
	const F_STATUS = 'status';
	const F_GLOBAL_ROLE = 'global_role';
	const F_ASSIGNED_COURSES = 'assigned_courses';
	const F_ASSIGNED_COURSES_DESKTOP = 'assigned_courses_desktop';
	const F_UNSUBSCRIBE_COURSES = 'unsubscribe_courses';
	const F_ASSIGNED_GROUPS = 'assigned_groups';
	const F_ASSIGNED_GROUPS_DESKTOP = 'assigned_groups_desktop';
	const F_PORTFOLIO_TEMPLATE_ID = 'portfolio_template_id';
	const F_PORTFOLIO_ASSIGNED_TO_GROUPS = 'portfolio_assigned_to_groups';
	const F_ASSIGNED_ORGUS = 'assigned_orgus';
	const F_ASSIGNED_STUDYPROGRAMS = 'assigned_studyprograms';
	const F_DESCRIPTION = 'description';
	const F_PORTFOLIO_NAME = 'portfolio_name';
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
	 */
	protected function txt($key) {
		return self::plugin()->translate( $key,'set');
	}


	protected function initForm() {
		$this->setTitle(self::plugin()->translate('form_title'));
		$te = new ilTextInputGUI($this->txt(self::F_TITLE), self::F_TITLE);
		$te->setRequired(true);
		$this->addItem($te);

		$te = new ilTextAreaInputGUI($this->txt(self::F_DESCRIPTION), self::F_DESCRIPTION);
		$this->addItem($te);

		$cb = new ilCheckboxInputGUI($this->txt(self::F_STATUS), self::F_STATUS);
		//		$this->addItem($cb);

		$a_item = new ilFormSectionHeaderGUI();
		$a_item->setTitle($this->txt(self::F_APPLICATION));
		$this->addItem($a_item);
		$this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_CREATE), self::F_ON_CREATE));
		$this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_UPDATE), self::F_ON_UPDATE));
		$this->addItem(new ilCheckboxInputGUI($this->txt(self::F_ON_MANUAL), self::F_ON_MANUAL));

		$a_item = new ilFormSectionHeaderGUI();
		$a_item->setTitle($this->txt('specific_settings'));
		$this->addItem($a_item);

		$se = new ilSelectInputGUI($this->txt(self::F_GLOBAL_ROLE), self::F_GLOBAL_ROLE);
		$se->setRequired(true);
		$global_roles = array( "" => $this->txt("form_please_choose") );
		self::appendRoles($global_roles, ilRbacReview::FILTER_ALL_GLOBAL);
		$se->setOptions($global_roles);
		$this->addItem($se);

		/// Assigned Courses
		$multiSelect = new udfMultiLineInputGUI($this->txt(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS), "MultiGroup");
		$multiSelect->setShowLabel(true);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('crs', $this->txt(self::F_ASSIGNED_COURSES), self::F_ASSIGNED_COURSES);
		$ilCourseMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('crs', $this->txt(self::F_ASSIGNED_COURSES_DESKTOP), self::F_ASSIGNED_COURSES_DESKTOP);
		$ilCourseMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$ilCheckboxInputGUI = new ilCheckboxInputGUI($this->txt(self::F_UNSUBSCRIBE_COURSES), self::F_UNSUBSCRIBE_COURSES);
		$this->addItem($ilCheckboxInputGUI);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_ASSIGNED_GROUPS), self::F_ASSIGNED_GROUPS);
		$ilCourseMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_ASSIGNED_GROUPS_DESKTOP), self::F_ASSIGNED_GROUPS_DESKTOP);
		$ilCourseMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

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

		$te = new ilTextInputGUI($this->txt(self::F_BLOG_NAME), self::F_BLOG_NAME);
		$this->addItem($te);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS), self::F_PORTFOLIO_ASSIGNED_TO_GROUPS);
		$ilCourseMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$ilOrgUnitMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('orgu', $this->txt(self::F_ASSIGNED_ORGUS), self::F_ASSIGNED_ORGUS);
		$ilOrgUnitMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilOrgUnitMultiSelectInputGUI);

		$ilStudyProgramMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('prg', $this->txt(self::F_ASSIGNED_STUDYPROGRAMS), self::F_ASSIGNED_STUDYPROGRAMS);
		$ilStudyProgramMultiSelectInputGUI->setAjaxLink(self::dic()->ctrl()->getLinkTarget($this->parent_gui, UserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilStudyProgramMultiSelectInputGUI);

		$this->addCommandButtons();
	}


	/**
	 * @param $existing_array
	 * @param $filter
	 *
	 * @return array
	 */
	protected static function appendRoles(array &$existing_array, $filter) {
		foreach (self::dic()->rbacreview()->getRolesByFilter($filter) as $role) {

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
		$array = array(
			self::F_TITLE => $this->object->getTitle(),
			self::F_DESCRIPTION => $this->object->getDescription(),
			//			self::F_STATUS => ($this->object->getStatus() == ilUserSetting::STATUS_ACTIVE ? 1 : 0),
			self::F_ASSIGNED_COURSES => implode(',', $this->object->getAssignedCourses()),
			self::F_ASSIGNED_COURSES_DESKTOP => implode(',', $this->object->getAssignedCoursesDesktop()),
			self::F_UNSUBSCRIBE_COURSES => $this->object->isUnsubscribeCoursesDesktop(),
			self::F_ASSIGNED_GROUPS => implode(',', $this->object->getAssignedGroupes()),
			self::F_ASSIGNED_GROUPS_DESKTOP => implode(',', $this->object->getAssignedGroupesDesktop()),
			self::F_GLOBAL_ROLE => $this->object->getGlobalRole(),
			self::F_PORTFOLIO_TEMPLATE_ID => $this->object->getPortfolioTemplateId(),
			self::F_PORTFOLIO_ASSIGNED_TO_GROUPS => implode(',', $this->object->getPortfolioAssignedToGroups()),
			self::F_BLOG_NAME => $this->object->getBlogName(),
			self::F_PORTFOLIO_NAME => $this->object->getPortfolioName(),
			self::F_ASSIGNED_ORGUS => implode(',', $this->object->getAssignedOrgus()),
			self::F_ASSIGNED_STUDYPROGRAMS => implode(',', $this->object->getAssignedStudyprograms()),
			self::F_ON_CREATE => $this->object->isOnCreate(),
			self::F_ON_UPDATE => $this->object->isOnUpdate(),
			self::F_ON_MANUAL => $this->object->isOnManual(),
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
		//		$this->object->setStatus($this->getInput(self::F_STATUS));
		$assigned_courses = $this->getInput(self::F_ASSIGNED_COURSES);
		$this->object->setAssignedCourses(explode(',', $assigned_courses[0]));
		$assigned_courses_desktop = $this->getInput(self::F_ASSIGNED_COURSES_DESKTOP);
		$this->object->setAssignedCoursesDesktop(explode(',', $assigned_courses_desktop[0]));
		$this->object->setUnsubscribeCoursesDesktop($this->getInput(self::F_UNSUBSCRIBE_COURSES));
		$assigned_groups = $this->getInput(self::F_ASSIGNED_GROUPS);
		$this->object->setAssignedGroupes(explode(',', $assigned_groups[0]));
		$assigned_groups_desktop = $this->getInput(self::F_ASSIGNED_GROUPS_DESKTOP);
		$this->object->setAssignedGroupesDesktop(explode(',', $assigned_groups_desktop[0]));
		$this->object->setGlobalRole($this->getInput(self::F_GLOBAL_ROLE));
		$portfolio_template_id = $this->getInput(self::F_PORTFOLIO_TEMPLATE_ID);
		$this->object->setPortfolioTemplateId($portfolio_template_id > 0 ? $portfolio_template_id : NULL);
		$portf_assigned_to_groups = $this->getInput(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS);
		$this->object->setPortfolioAssignedToGroups(explode(',', $portf_assigned_to_groups[0]));
		$this->object->setBlogName($this->getInput(self::F_BLOG_NAME));
		$this->object->setPortfolioName($this->getInput(self::F_PORTFOLIO_NAME));
		$assigned_orgus = $this->getInput(self::F_ASSIGNED_ORGUS);
		$this->object->setAssignedOrgus(explode(',', $assigned_orgus[0]));
		$assigned_studyprograms = $this->getInput(self::F_ASSIGNED_STUDYPROGRAMS);
		$this->object->setAssignedStudyprograms(explode(',', $assigned_studyprograms[0]));

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
