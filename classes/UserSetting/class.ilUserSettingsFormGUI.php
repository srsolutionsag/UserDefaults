<?php
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');
require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/Form/class.ilContainerMultiSelectInputGUI.php');

/**
 * Class ilUserSettingsFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserSettingsFormGUI extends ilPropertyFormGUI {

	const F_TITLE = 'title';
	const F_STATUS = 'status';
	const F_GLOBAL_ROLE = 'global_role';
	const F_ASSIGNED_COURSES = 'assigned_courses';
	const F_ASSIGNED_GROUPS = 'assigned_groupes';
	const F_PORTFOLIO_TEMPLATE_ID = 'portfolio_template_id';
	const F_PORTFOLIO_ASSIGNED_TO_GROUPS = 'portfolio_assigned_to_groups';
	const F_DESCRIPTION = 'description';
	/**
	 * @var ilUserSettingsGUI
	 */
	protected $parent_gui;
	/**
	 * @var ilUserSetting
	 */
	protected $object;


	/**
	 * @param ilUserSettingsGUI $parent_gui
	 * @param ilUserSetting     $ilUserSetting
	 */
	public function __construct(ilUserSettingsGUI $parent_gui, ilUserSetting $ilUserSetting) {

		global $ilCtrl;
		$this->parent_gui = $parent_gui;
		$this->object = $ilUserSetting;
		$this->ctrl = $ilCtrl;
		$this->pl = ilUserDefaultsPlugin::getInstance();
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initForm();
	}


	/**
	 * @param $key
	 *
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('set_' . $key);
	}


	protected function initForm() {
		$this->setTitle($this->pl->txt('form_title'));
		$te = new ilTextInputGUI($this->txt(self::F_TITLE), self::F_TITLE);
		$te->setRequired(true);
		$this->addItem($te);

		$this->setTitle($this->pl->txt('form_title'));
		$te = new ilTextAreaInputGUI($this->txt(self::F_DESCRIPTION), self::F_DESCRIPTION);
		$this->addItem($te);

		$cb = new ilCheckboxInputGUI($this->txt(self::F_STATUS), self::F_STATUS);
		//		$this->addItem($cb);

		$se = new ilSelectInputGUI($this->txt(self::F_GLOBAL_ROLE), self::F_GLOBAL_ROLE);
		$se->setRequired(true);
		$global_roles = self::getRoles(ilRbacReview::FILTER_ALL_GLOBAL);
		$se->setOptions($global_roles);
		$this->addItem($se);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('crs', $this->txt(self::F_ASSIGNED_COURSES), self::F_ASSIGNED_COURSES);
		$ilCourseMultiSelectInputGUI->setAjaxLink($this->ctrl->getLinkTarget($this->parent_gui, ilUserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_ASSIGNED_GROUPS), self::F_ASSIGNED_GROUPS);
		$ilCourseMultiSelectInputGUI->setAjaxLink($this->ctrl->getLinkTarget($this->parent_gui, ilUserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$se = new ilSelectInputGUI($this->txt(self::F_PORTFOLIO_TEMPLATE_ID), self::F_PORTFOLIO_TEMPLATE_ID);
		$se->setRequired(true);
		$se->setOptions(ilObjPortfolioTemplate::getAvailablePortfolioTemplates());
		$this->addItem($se);

		$ilCourseMultiSelectInputGUI = new ilContainerMultiSelectInputGUI('grp', $this->txt(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS), self::F_PORTFOLIO_ASSIGNED_TO_GROUPS);
		$ilCourseMultiSelectInputGUI->setAjaxLink($this->ctrl->getLinkTarget($this->parent_gui, ilUserSettingsGUI::CMD_SEARCH_COURSES));
		$this->addItem($ilCourseMultiSelectInputGUI);

		$this->addCommandButtons();
	}


	/**
	 * @param int  $filter
	 * @param bool $with_text
	 *
	 * @return array
	 */
	public static function getRoles($filter, $with_text = true) {
		global $rbacreview;
		$opt = array();
		$role_ids = array();
		foreach ($rbacreview->getRolesByFilter($filter) as $role) {
			$opt[$role['obj_id']] = $role[self::F_TITLE] . ' (' . $role['obj_id'] . ')';
			$role_ids[] = $role['obj_id'];
		}
		if ($with_text) {
			return $opt;
		} else {
			return $role_ids;
		}
	}


	public function fillForm() {
		$array = array(
			self::F_TITLE => $this->object->getTitle(),
			self::F_DESCRIPTION => $this->object->getDescription(),
			//			self::F_STATUS => ($this->object->getStatus() == ilUserSetting::STATUS_ACTIVE ? 1 : 0),
			self::F_ASSIGNED_COURSES => implode(',', $this->object->getAssignedCourses()),
			self::F_ASSIGNED_GROUPS => implode(',', $this->object->getAssignedGroupes()),
			self::F_GLOBAL_ROLE => $this->object->getGlobalRole(),
			self::F_PORTFOLIO_TEMPLATE_ID => $this->object->getPortfolioTemplateId(),
			self::F_PORTFOLIO_ASSIGNED_TO_GROUPS => implode(',', $this->object->getPortfolioAssignedToGroups()),
		);

		$this->setValuesByArray($array);
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (! $this->checkInput()) {
			return false;
		}
		$this->object->setTitle($this->getInput(self::F_TITLE));
		$this->object->setDescription($this->getInput(self::F_DESCRIPTION));
		//		$this->object->setStatus($this->getInput(self::F_STATUS));
		$assigned_courses = $this->getInput(self::F_ASSIGNED_COURSES);
		$this->object->setAssignedCourses(explode(',', $assigned_courses[0]));
		$assigned_groups = $this->getInput(self::F_ASSIGNED_GROUPS);
		$this->object->setAssignedGroupes(explode(',', $assigned_groups[0]));
		$this->object->setGlobalRole($this->getInput(self::F_GLOBAL_ROLE));
		$this->object->setPortfolioTemplateId($this->getInput(self::F_PORTFOLIO_TEMPLATE_ID));
		$portf_assignt_to_groups = $this->getInput(self::F_PORTFOLIO_ASSIGNED_TO_GROUPS);
		$this->object->setPortfolioAssignedToGroups(explode(',', $portf_assignt_to_groups[0]));

		if ($this->object->getId() > 0) {
			$this->object->update();
		} else {
			$this->object->create();
		}

		return true;
	}


	protected function addCommandButtons() {
		if ($this->object->getId() > 0) {
			$this->addCommandButton(ilUserSettingsGUI::CMD_UPDATE, $this->pl->txt('form_button_update'));
		} else {
			$this->addCommandButton(ilUserSettingsGUI::CMD_CREATE, $this->pl->txt('form_button_create'));
		}
		$this->addCommandButton(ilUserSettingsGUI::CMD_CANCEL, $this->pl->txt('form_button_cancel'));
	}
}

?>
