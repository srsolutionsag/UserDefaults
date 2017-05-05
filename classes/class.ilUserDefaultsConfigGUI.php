<?php

require_once('./Services/Component/classes/class.ilPluginConfigGUI.php');
require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/UserSetting/class.ilUserSettingsGUI.php');

/**
 * ilUserDefaultsConfigGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 1.0.00
 * ilCtrl_IsCalledBy ilUserDefaultsConfigGUI : ilObjComponentSettingsGUI
 */
class ilUserDefaultsConfigGUI extends ilPluginConfigGUI {

	public function executeCommand() {
		global $ilCtrl, $ilTabs, $lng, $tpl;

		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "ctype", $_GET["ctype"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "cname", $_GET["cname"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "slot_id", $_GET["slot_id"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "plugin_id", $_GET["plugin_id"]);
		$ilCtrl->setParameterByClass("ilobjcomponentsettingsgui", "pname", $_GET["pname"]);

		$tpl->setTitle($lng->txt("cmps_plugin") . ": " . $_GET["pname"]);
		$tpl->setDescription("");
		/**
		 * @var $ilTabs ilTabsGUI
		 */
		/**
		 * @var $ilCtrl ilCtrl
		 */

		$ilTabs->clearTargets();

		$ilTabs->addTab('settings', $this->plugin_object->txt('tabs_settings'), $ilCtrl->getLinkTargetByClass('ilUserSettingsGUI'));
		$ilTabs->addTab('users', $this->plugin_object->txt('tabs_users'), $ilCtrl->getLinkTargetByClass('usrdefUserGUI'));


		$nextClass = $ilCtrl->getNextClass();
		switch ($nextClass) {
			case 'iludfcheckgui':
				$ilTabs->activateTab('settings');
				$ilUDFCheckGUI = new ilUDFCheckGUI(new ilUserSettingsGUI($this));
				$ilCtrl->forwardCommand($ilUDFCheckGUI);
				break;
			case 'usrdefusergui':
				require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/UserSearch/class.usrdefUserGUI.php');
				$ilTabs->activateTab('users');
				$usrdefUserGUI = new usrdefUserGUI();
				$ilCtrl->forwardCommand($usrdefUserGUI);
				break;
			default;
				$ilTabs->activateTab('settings');
				$ilUserSettingsGUI = new ilUserSettingsGUI($this);
				$ilCtrl->forwardCommand($ilUserSettingsGUI);
				break;
		}
	}


	public function performCommand($cmd) {
	}
}

