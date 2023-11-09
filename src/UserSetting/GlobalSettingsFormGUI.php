<?php

namespace srag\Plugins\UserDefaults\UserSetting;

use arException;
use ilNumberInputGUI;
use ilPropertyFormGUI;
use ilUserDefaultsPlugin;
use RectorPrefix202302\SebastianBergmann\Diff\Exception;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Config\UserDefaultsConfig;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UserDefaultsGlobalSettingsGUI;

/**
 * Class GlobalSettingsFormGUI
 *
 * @package srag\Plugins\UserDefaults\UserSetting
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class GlobalSettingsFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected UserDefaultsGlobalSettingsGUI $parent;


	/**
	 * GlobalSettingsFormGUI constructor
	 */
	public function __construct(UserDefaultsGlobalSettingsGUI $parent) {
		parent::__construct();

		$this->parent = $parent;

		$this->initForm();
	}


	protected function initForm(): void
    {
		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent));

		$this->setTitle(self::plugin()->translate("tabs_global_settings"));

		$this->addCommandButton(UserDefaultsGlobalSettingsGUI::CMD_UPDATE_CONFIGURE, self::plugin()->translate("save"));

		$category_ref_id = new ilNumberInputGUI(self::plugin()->translate(UserDefaultsConfig::KEY_CATEGORY_REF_ID), UserDefaultsConfig::KEY_CATEGORY_REF_ID);
		$category_ref_id->setInfo(self::plugin()->translate(UserDefaultsConfig::KEY_CATEGORY_REF_ID . "_info"));
        try {
            /** @var UserDefaultsConfig $userDefaultsConfig */
            $userDefaultsConfig = UserDefaultsConfig::findOrGetInstance(UserDefaultsConfig::KEY_CATEGORY_REF_ID);
            $category_ref_id->setValue($userDefaultsConfig->getValue());
        } catch (arException $ex) {
            //
        }
		$this->addItem($category_ref_id);
	}

	public function updateConfigure(): void
    {
		$category_ref_id = $this->getInput(UserDefaultsConfig::KEY_CATEGORY_REF_ID);
        /**
         * @var UserDefaultsConfig $userDefaultsConfig
         */
        try {
            $userDefaultsConfig = UserDefaultsConfig::findOrGetInstance(UserDefaultsConfig::KEY_CATEGORY_REF_ID);
        } catch (arException $ex) {
            $userDefaultsConfig = new UserDefaultsConfig();
        }
        $userDefaultsConfig->setName(UserDefaultsConfig::KEY_CATEGORY_REF_ID);
        $userDefaultsConfig->setValue($category_ref_id);
        $userDefaultsConfig->store();

        //$userDefaultsConfig->setValue($category_ref_id);
        //$userDefaultsConfig->update();

		//todo
        //UserDefaultsConfig::setField(UserDefaultsConfig::KEY_CATEGORY_REF_ID, $category_ref_id);
	}
}
