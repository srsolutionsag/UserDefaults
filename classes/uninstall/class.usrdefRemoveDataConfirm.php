<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use srag\RemovePluginDataConfirm\UserDefaults\AbstractRemovePluginDataConfirm;

/**
 * Class usrdefRemoveDataConfirm
 *
 * @ilCtrl_isCalledBy usrdefRemoveDataConfirm: ilUIPluginRouterGUI
 */
class usrdefRemoveDataConfirm extends AbstractRemovePluginDataConfirm {

	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
}
