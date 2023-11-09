<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\UserDefaults\DICTrait;
use srag\DIC\UserDefaults\Exception\DICException;
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


	public function executeCommand(): void
    {
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

	protected function getGlobalSettingsForm(): GlobalSettingsFormGUI
    {
		$form = new GlobalSettingsFormGUI($this);

		return $form;
	}


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    protected function configure(): void
    {
		$form = $this->getGlobalSettingsForm();

		self::output()->output($form);
	}


    /**
     * @throws DICException
     * @throws ilCtrlException
     * @throws ilTemplateException
     */
    protected function updateConfigure(): void
    {
		$form = $this->getGlobalSettingsForm();
		$form->setValuesByPost();

		if (!$form->checkInput()) {
			self::output()->output($form);

			return;
		}

		$form->updateConfigure();

        global $DIC;
        $tpl = $DIC["tpl"];
        $tpl->setOnScreenMessage('success',self::plugin()->translate('saved'), true);


		self::dic()->ctrl()->redirectByClass(self::class, self::CMD_CONFIGURE);
	}
}
