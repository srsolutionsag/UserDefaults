<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * ilUserDefaultsConfigGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 1.0.00
 * ilCtrl_IsCalledBy ilUserDefaultsConfigGUI : ilObjComponentSettingsGUI
 */
class ilUserDefaultsConfigGUI extends ilPluginConfigGUI {

	const TAB_SETTINGS = "settings";
	const TAB_USERS = "users";
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var ilLanguage
	 */
	protected $lng;
	/**
	 * @var ilTabsGUI
	 */
	protected $tabs;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	function __construct() {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->lng = $DIC->language();
		$this->tabs = $DIC->tabs();
		$this->tpl = $DIC->ui()->mainTemplate();
	}


	public function executeCommand() {
		$this->ctrl->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $_GET["ctype"]);
		$this->ctrl->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $_GET["cname"]);
		$this->ctrl->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $_GET["slot_id"]);
		$this->ctrl->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $_GET["plugin_id"]);
		$this->ctrl->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $_GET["pname"]);

		$this->tpl->setTitle($this->lng->txt("cmps_plugin") . ": " . $_GET["pname"]);
		$this->tpl->setDescription("");

		$this->tabs->clearTargets();

		$this->tabs->addTab(self::TAB_SETTINGS, $this->plugin_object->txt('tabs_settings'), $this->ctrl->getLinkTargetByClass(ilUserSettingsGUI::class));
		$this->tabs->addTab(self::TAB_USERS, $this->plugin_object->txt('tabs_users'), $this->ctrl->getLinkTargetByClass(usrdefUserGUI::class));

		$nextClass = $this->ctrl->getNextClass();
		switch ($nextClass) {
			case strtolower(ilUDFCheckGUI::class):
				$this->tabs->activateTab(self::TAB_SETTINGS);
				$ilUDFCheckGUI = new ilUDFCheckGUI(new ilUserSettingsGUI($this));
				$this->ctrl->forwardCommand($ilUDFCheckGUI);
				break;
			case strtolower(usrdefUserGUI::class):
				$this->tabs->activateTab(self::TAB_USERS);
				$usrdefUserGUI = new usrdefUserGUI();
				$this->ctrl->forwardCommand($usrdefUserGUI);
				break;
			default;
				$this->tabs->activateTab(self::TAB_SETTINGS);
				$ilUserSettingsGUI = new ilUserSettingsGUI($this);
				$this->ctrl->forwardCommand($ilUserSettingsGUI);
				break;
		}
	}


	public function performCommand($cmd) {
	}
}
