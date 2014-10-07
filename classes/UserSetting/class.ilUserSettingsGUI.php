<?php
require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/UserSetting/class.ilUserSettingsFormGUI.php');

/**
 * Class ilUserSettingsGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy ilUserSettingsGUI : ilUserDefaultsConfigGUI
 */
class ilUserSettingsGUI {

	const CDM_DEFAULT = 'configure';
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var HTML_Template_ITX|ilTemplate
	 */
	protected $tpl;


	/**
	 * @param $parent_gui
	 */
	public function __construct($parent_gui) {
		global $ilCtrl, $tpl;
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
	}


	public function executeCommand() {
		$cmd = $this->ctrl->getCmd(self::CDM_DEFAULT);
		switch ($cmd) {
			case self::CDM_DEFAULT:
				$this->index();
				break;
		}

		return true;
	}


	protected function index() {
		$ilUserSettingsFormGUI = new ilUserSettingsFormGUI($this, new ilUserSetting());
		$ilUserSettingsFormGUI->fillForm();
		$this->tpl->setContent($ilUserSettingsFormGUI->getHTML());
	}
}

?>
