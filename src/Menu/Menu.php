<?php

namespace srag\Plugins\UserDefaults\Menu;

use ilAdministrationGUI;
use ilCtrlException;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ilObjComponentSettingsGUI;
use ilUserDefaultsConfigGUI;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

class Menu extends AbstractStaticPluginMainMenuProvider {
    use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    private \ilCtrlInterface $ctrl;
    private ilUserDefaultsPlugin $pl;
    private \ILIAS\DI\UIServices $ui;
    private \ILIAS\DI\RBACServices $rbac;
    private \ilObjUser $user;

    public function __construct(\ILIAS\DI\Container $dic) {
        $this->ctrl = $dic->ctrl();
        $this->ui = $dic->ui();
        $this->rbac = $dic->rbac();
        $this->user = $dic->user();
        $this->pl = ilUserDefaultsPlugin::getInstance();
    }


	/**
	 * @inheritdoc
	 */
	public function getStaticTopItems(): array {
		return [
           $this->mainmenu->topParentItem($this->if->identifier(ilUserDefaultsPlugin::PLUGIN_ID . "_top"))->withTitle(ilUserDefaultsPlugin::PLUGIN_NAME)
				->withAvailableCallable(function (): bool {
					return $this->pl->isActive();
				})->withVisibilityCallable(function (): bool {
					return $this->rbac->review()->isAssigned($this->user->getId(), 2); // Default admin role
				})
		];
	}


    /**
     * @inheritdoc
     * @throws ilCtrlException
     */
	public function getStaticSubItems(): array {
		$parent = $this->getStaticTopItems()[0];

        $this->ctrl->setParameterByClass(ilUserDefaultsConfigGUI::class, "ref_id", 31);
        $this->ctrl->setParameterByClass(ilUserDefaultsConfigGUI::class, "ctype", "Services");
        $this->ctrl->setParameterByClass(ilUserDefaultsConfigGUI::class, "cname", "EventHandling");
        $this->ctrl->setParameterByClass(ilUserDefaultsConfigGUI::class, "slot_id", "evhk");
        $this->ctrl->setParameterByClass(ilUserDefaultsConfigGUI::class, "pname", ilUserDefaultsPlugin::PLUGIN_NAME);

		return [
			$this->mainmenu->link($this->if->identifier(ilUserDefaultsPlugin::PLUGIN_ID . "_configuration"))
				->withParent($parent->getProviderIdentification())->withTitle(ilUserDefaultsPlugin::PLUGIN_NAME)->withAction($this->ctrl
					->getLinkTargetByClass([
						ilAdministrationGUI::class,
						ilObjComponentSettingsGUI::class,
						ilUserDefaultsConfigGUI::class
					], ""))->withAvailableCallable(function (): bool {
					return $this->pl->isActive();
				})->withVisibilityCallable(function (): bool {
					return $this->rbac->review()->isAssigned($this->user->getId(), 2); // Default admin role
				})
		];
	}
}
