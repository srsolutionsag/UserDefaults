<?php
require_once __DIR__ . "/../../vendor/autoload.php";

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
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const CMD_CONFIGURE = "configure";
	const CMD_UPDATE_CONFIGURE = "updateConfigure";

    private ilCtrl $ctrl;
    private ilUserDefaultsPlugin $pl;
    private ilGlobalTemplateInterface $tpl;
    private \ILIAS\DI\UIServices $ui;

    public function __construct() {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->ui = $DIC->ui();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilUserDefaultsPlugin::getInstance();
	}

    /**
     * @throws ilCtrlException
     */
    public function executeCommand(): void
    {
		$next_class = $this->ctrl->getNextClass($this);
        $cmd = $this->ctrl->getCmd();
        switch ($cmd) {
            case self::CMD_CONFIGURE:
            case self::CMD_UPDATE_CONFIGURE:
                $this->{$cmd}();
                break;

            default:
                break;
        }
    }

	protected function getGlobalSettingsForm(): GlobalSettingsFormGUI
    {
        return new GlobalSettingsFormGUI($this);
	}

    protected function configure(): void
    {
		$form = $this->getGlobalSettingsForm();
        $this->ui->mainTemplate()->setContent($form->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    protected function updateConfigure(): void
    {
		$form = $this->getGlobalSettingsForm();
		$form->setValuesByPost();
		if (!$form->checkInput()) {
            $this->ui->mainTemplate()->setContent($form->getHTML());
			return;
		}
		$form->updateConfigure();
        $this->tpl->setOnScreenMessage('success',$this->pl->txt('saved'), true);
        $this->ctrl->redirectByClass(self::class, self::CMD_CONFIGURE);
	}
}
