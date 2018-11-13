<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\UserSetting\GlobalSettingsFormGUI;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class UserDefaultsGlobalSettingsGUI
 *
 * @author            studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @ilCtrl_IsCalledBy UserDefaultsGlobalSettingsGUI : ilUserDefaultsConfigGUI
 */
class UserDefaultsGlobalSettingsGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const CMD_CONFIGURE = "configure";
	const CMD_UPDATE_CONFIGURE = "updateConfigure";


	/**
	 * UserDefaultsGlobalSettingsGUI constructor
	 */
	public function __construct() {

	}


	/**
	 *
	 */
	public function executeCommand() {
		$next_class = self::dic()->ctrl()->getNextClass($this);

		switch (strtolower($next_class)) {
			default:
				$cmd = self::dic()->ctrl()->getCmd();

				switch ($cmd) {
					case self::CMD_CONFIGURE:
					case self::CMD_UPDATE_CONFIGURE:
						$this->{$cmd}();
						break;

					default:
						break;
				}
				break;
		}
	}


	/**
	 * @return GlobalSettingsFormGUI
	 */
	protected function getGlobalSettingsForm() {
		$form = new GlobalSettingsFormGUI($this);

		return $form;
	}


	/**
	 *
	 */
	protected function configure() {
		$form = $this->getGlobalSettingsForm();

		self::plugin()->output($form);
	}


	/**
	 *
	 */
	protected function updateConfigure() {
		$form = $this->getGlobalSettingsForm();
		$form->setValuesByPost();

		if (!$form->checkInput()) {
			self::plugin()->output($form);

			return;
		}

		$form->updateConfigure();

		ilUtil::sendSuccess(self::plugin()->translate("saved"), true);

		self::dic()->ctrl()->redirectByClass(self::class, self::CMD_CONFIGURE);
	}
}
