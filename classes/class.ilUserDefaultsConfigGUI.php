<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class ilUserDefaultsConfigGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 *
 * @version 1.0.00
 * ilCtrl_IsCalledBy ilUserDefaultsConfigGUI : ilObjComponentSettingsGUI
 */
class ilUserDefaultsConfigGUI extends ilPluginConfigGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const TAB_SETTINGS = "settings";
	const TAB_USERS = "users";


	function __construct() {

	}


	public function executeCommand() {
		// TODO: Refactoring
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "ctype", $_GET["ctype"]);
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "cname", $_GET["cname"]);
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "slot_id", $_GET["slot_id"]);
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "plugin_id", $_GET["plugin_id"]);
		self::dic()->ctrl()->setParameterByClass(ilObjComponentSettingsGUI::class, "pname", $_GET["pname"]);

		self::dic()->template()->setTitle(self::dic()->language()->txt("cmps_plugin") . ": " . $_GET["pname"]);
		self::dic()->template()->setDescription("");

		self::dic()->tabs()->clearTargets();

		self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate('tabs_settings'), self::dic()->ctrl()
			->getLinkTargetByClass(UserSettingsGUI::class));
		self::dic()->tabs()->addTab(self::TAB_USERS, self::plugin()->translate('tabs_users'), self::dic()->ctrl()
			->getLinkTargetByClass(usrdefUserGUI::class));

		$nextClass = self::dic()->ctrl()->getNextClass();
		switch ($nextClass) {
			case strtolower(UDFCheckGUI::class):
				self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
				$ilUDFCheckGUI = new UDFCheckGUI(new UserSettingsGUI($this));
				self::dic()->ctrl()->forwardCommand($ilUDFCheckGUI);
				break;
			case strtolower(usrdefUserGUI::class):
				self::dic()->tabs()->activateTab(self::TAB_USERS);
				$usrdefUserGUI = new usrdefUserGUI();
				self::dic()->ctrl()->forwardCommand($usrdefUserGUI);
				break;
			default;
				self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
				$ilUserSettingsGUI = new UserSettingsGUI($this);
				self::dic()->ctrl()->forwardCommand($ilUserSettingsGUI);
				break;
		}
	}


	public function performCommand($cmd) {
	}
}
