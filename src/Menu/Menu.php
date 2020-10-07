<?php

namespace srag\Plugins\UserDefaults\Menu;

use ilAdministrationGUI;
use ILIAS\GlobalScreen\Scope\MainMenu\Factory\AbstractBaseItem;
use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use ILIAS\UI\Component\Symbol\Icon\Standard;
use ilObjComponentSettingsGUI;
use ilUserDefaultsConfigGUI;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

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
		return [
            $this->symbol($this->mainmenu->topParentItem($this->if->identifier(ilUserDefaultsPlugin::PLUGIN_ID . "_top"))->withTitle(ilUserDefaultsPlugin::PLUGIN_NAME)
				->withAvailableCallable(function (): bool {
					return self::plugin()->getPluginObject()->isActive();
				})->withVisibilityCallable(function (): bool {
					return self::dic()->rbacreview()->isAssigned(self::dic()->user()->getId(), 2); // Default admin role
				}))
		];
	}


	/**
	 * @inheritdoc
	 */
	public function getStaticSubItems(): array {
		$parent = $this->getStaticTopItems()[0];

		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "ref_id", 31);
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "ctype", IL_COMP_SERVICE);
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "cname", "EventHandling");
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "slot_id", "evhk");
		self::dic()->ctrl()->setParameterByClass(ilUserDefaultsConfigGUI::class, "pname", ilUserDefaultsPlugin::PLUGIN_NAME);

		return [
			$this->symbol($this->mainmenu->link($this->if->identifier(ilUserDefaultsPlugin::PLUGIN_ID . "_configuration"))
				->withParent($parent->getProviderIdentification())->withTitle(ilUserDefaultsPlugin::PLUGIN_NAME)->withAction(self::dic()->ctrl()
					->getLinkTargetByClass([
						ilAdministrationGUI::class,
						ilObjComponentSettingsGUI::class,
						ilUserDefaultsConfigGUI::class
					], ""))->withAvailableCallable(function (): bool {
					return self::plugin()->getPluginObject()->isActive();
				})->withVisibilityCallable(function (): bool {
					return self::dic()->rbacreview()->isAssigned(self::dic()->user()->getId(), 2); // Default admin role
				}))
		];
	}


    /**
     * @param AbstractBaseItem $entry
     *
     * @return AbstractBaseItem
     */
    protected function symbol(AbstractBaseItem $entry) : AbstractBaseItem
    {
        if (self::version()->is6()) {
            $entry = $entry->withSymbol(self::dic()->ui()->factory()->symbol()->icon()->standard(Standard::USR, ilUserDefaultsPlugin::PLUGIN_NAME)->withIsOutlined(true));
        }

        return $entry;
    }
}
