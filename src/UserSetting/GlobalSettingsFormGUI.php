<?php

namespace srag\Plugins\UserDefaults\UserSetting;

use arException;
use ilNumberInputGUI;
use ilPropertyFormGUI;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Config\UserDefaultsConfig;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UserDefaultsGlobalSettingsGUI;
class GlobalSettingsFormGUI extends ilPropertyFormGUI {

    use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected UserDefaultsGlobalSettingsGUI $parent;
    
	public function __construct(UserDefaultsGlobalSettingsGUI $parent) {
        global $DIC;
		parent::__construct();
        $this->ctrl = $DIC->ctrl();
        $this->pl = ilUserDefaultsPlugin::getInstance();

		$this->parent = $parent;

		$this->initForm();
	}


	protected function initForm(): void
    {
		$this->setFormAction($this->ctrl->getFormAction($this->parent));

		$this->setTitle($this->pl->txt("tabs_global_settings"));

		$this->addCommandButton(UserDefaultsGlobalSettingsGUI::CMD_UPDATE_CONFIGURE, $this->pl->txt("save"));

		$category_ref_id = new ilNumberInputGUI($this->pl->txt(UserDefaultsConfig::KEY_CATEGORY_REF_ID), UserDefaultsConfig::KEY_CATEGORY_REF_ID);
		$category_ref_id->setInfo($this->pl->txt(UserDefaultsConfig::KEY_CATEGORY_REF_ID . "_info"));
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
	}
}
