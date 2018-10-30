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
	const TAB_GLOBAL_SETTINGS = "global_settings";


	/**
	 * ilUserDefaultsConfigGUI constructor
	 */
	public function __construct() {

	}


	/**
	 * @param string $cmd
	 */
	public function performCommand($cmd) {
		self::dic()->tabs()->addTab(self::TAB_SETTINGS, self::plugin()->translate('tabs_settings'), self::dic()->ctrl()
			->getLinkTargetByClass(UserSettingsGUI::class));
		self::dic()->tabs()->addTab(self::TAB_USERS, self::plugin()->translate('tabs_users'), self::dic()->ctrl()
			->getLinkTargetByClass(usrdefUserGUI::class));
		self::dic()->tabs()->addTab(self::TAB_GLOBAL_SETTINGS, self::plugin()->translate('tabs_global_settings'), self::dic()->ctrl()
			->getLinkTargetByClass(UserDefaultsGlobalSettingsGUI::class, UserDefaultsGlobalSettingsGUI::CMD_CONFIGURE));

		$nextClass = self::dic()->ctrl()->getNextClass();
		switch ($nextClass) {
			case strtolower(UDFCheckGUI::class):
				self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
				$gui = new UDFCheckGUI(new UserSettingsGUI($this));
				self::dic()->ctrl()->forwardCommand($gui);
				break;
			case strtolower(usrdefUserGUI::class):
				self::dic()->tabs()->activateTab(self::TAB_USERS);
				$gui = new usrdefUserGUI();
				self::dic()->ctrl()->forwardCommand($gui);
				break;
			case strtolower(UserDefaultsGlobalSettingsGUI::class):
				self::dic()->tabs()->activateTab(self::TAB_GLOBAL_SETTINGS);
				$gui = new UserDefaultsGlobalSettingsGUI();
				self::dic()->ctrl()->forwardCommand($gui);
				break;
			default;
				self::dic()->tabs()->activateTab(self::TAB_SETTINGS);
				$gui = new UserSettingsGUI($this);
				self::dic()->ctrl()->forwardCommand($gui);
				break;
		}
	}
}
