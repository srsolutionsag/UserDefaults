<?php

namespace srag\Plugins\UserDefaults\Menu;

use ilAdministrationGUI;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ilObjComponentSettingsGUI;
use ilUserDefaultsConfigGUI;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UserDefaultsMainGUI;

/**
 * Class Menu
 *
 * @package srag\Plugins\UserDefaults\Menu
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 *
 * @since   ILIAS 5.4
 */
class Menu extends AbstractStaticPluginMainMenuProvider {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getStaticTopItems(): array {
		if (!self::plugin()->getPluginObject()->isActive()) {
			return [];
		}

		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "ref_id", 31);
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "ctype", IL_COMP_SERVICE);
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "cname", "Cron");
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "slot_id", "crnhk");
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "pname", ilUserDefaultsPlugin::PLUGIN_NAME);

		return [
			self::dic()->globalScreen()->mainmenu()->topLinkItem(self::dic()->globalScreen()->identification()->plugin(self::plugin()
				->getPluginObject(), $this)->identifier(ilUserDefaultsPlugin::PLUGIN_ID))->withTitle(ilUserDefaultsPlugin::PLUGIN_NAME)
				->withAction(self::dic()->ctrl()->getLinkTargetByClass([
						ilAdministrationGUI::class,
						ilObjComponentSettingsGUI::class,
						ilUserDefaultsConfigGUI::class,
					], ""))->withAvailableCallable(function (): bool {
				return self::plugin()->getPluginObject()->isActive();
			})->withVisibilityCallable(function (): bool {
				return self::dic()->rbacreview()->isAssigned(self::dic()->user()->getId(), 2); // Default admin role
			})
		];
	}


	/**
	 * @inheritdoc
	 */
	public function getStaticSubItems(): array {
		return [];
	}
}
