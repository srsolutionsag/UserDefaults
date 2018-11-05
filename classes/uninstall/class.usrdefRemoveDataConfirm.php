<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\UserDefaults\Config\Config;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use srag\RemovePluginDataConfirm\AbstractRemovePluginDataConfirm;

/**
 * Class usrdefRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy usrdefRemoveDataConfirm: ilUIPluginRouterGUI
 */
class usrdefRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;


	/**
	 * @inheritdoc
	 */
	public function getUninstallRemovesData()/*: ?bool*/ {
		return Config::getUninstallRemovesData();
	}


	/**
	 * @inheritdoc
	 */
	public function setUninstallRemovesData(/*bool*/
		$uninstall_removes_data)/*: void*/ {
		Config::setUninstallRemovesData($uninstall_removes_data);
	}


	/**
	 * @inheritdoc
	 */
	public function removeUninstallRemovesData()/*: void*/ {
		Config::removeUninstallRemovesData();
	}
}
