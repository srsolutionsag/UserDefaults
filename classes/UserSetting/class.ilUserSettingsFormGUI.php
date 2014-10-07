<?php
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');

/**
 * Class ilUserSettingsFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserSettingsFormGUI extends ilPropertyFormGUI {

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


	protected function initForm() {
		$this->setTitle($this->pl->txt('form_title'));
		$te = new ilTextInputGUI($this->pl->txt('title'), 'title');
		$te->setRequired(true);
		$this->addItem($te);

		$se = new ilSelectInputGUI($this->pl->txt('global_role'), 'global_role');
		$se->setRequired(true);

		$global_roles = self::getRoles(ilRbacReview::FILTER_ALL_GLOBAL);
		$se->setOptions($global_roles);
		$this->addItem($se);

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
			$opt[$role['obj_id']] = $role['title'] . ' (' . $role['obj_id'] . ')';
			$role_ids[] = $role['obj_id'];
		}
		if ($with_text) {
			return $opt;
		} else {
			return $role_ids;
		}
	}


	public function fillForm() {
		$array = array();

		$this->setValuesByArray($array);
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (! $this->checkInput()) {
			return false;
		}

		return true;
	}


	protected function addCommandButtons() {
		$this->addCommandButton('save', $this->pl->txt('admin_form_button_save'));
		$this->addCommandButton('cancel', $this->pl->txt('admin_form_button_cancel'));
	}
}

?>
