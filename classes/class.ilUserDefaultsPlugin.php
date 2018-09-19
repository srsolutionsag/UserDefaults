<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\UserDefaults\Config\Config;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheck;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\RemovePluginDataConfirm\PluginUninstallTrait;


/**
 * Class ilUserDefaultsPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserDefaultsPlugin extends ilEventHookPlugin {

	use PluginUninstallTrait;
	const PLUGIN_ID = 'usrdef';
	const PLUGIN_NAME = 'UserDefaults';
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = usrdefRemoveDataConfirm::class;
	// Known Components
	const SERVICES_USER = 'Services/User';
	const SERVICES_AUTHENTICATION = 'Services/Authentication';
	// Known Actions
	const CREATED_1 = 'saveAsNew';
	const CREATED_2 = 'afterCreate';
	const UPDATED = 'afterUpdate';
	const AFTER_LOGIN = 'afterLogin';
	/**
	 * @var
	 */
	protected static $instance;
	/**
	 * @var array
	 */
	protected static $mapping = array(
		self::CREATED_1 => 'on_create',
		self::CREATED_2 => 'on_create',
		self::UPDATED => 'on_update',
		self::AFTER_LOGIN => 'on_update',
	);


	/**
	 * @return ilUserDefaultsPlugin
	 */
	public static function getInstance() {
		if (!isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 *
	 */
	public function __construct() {
		parent::__construct();
	}


	/**
	 * Handle the event
	 *
	 * @param    string        component, e.g. "Services/User"
	 * @param    string         event, e.g. "afterUpdate"
	 * @param    array         array of event specific parameters
	 */
	public function handleEvent($a_component, $a_event, $a_parameter) {
		$run = false;
		$ilUser = NULL;
		switch ($a_component) {
			case self::SERVICES_AUTHENTICATION:
				switch ($a_event) {
					case self::AFTER_LOGIN:
						$user_id = ilObjUser::getUserIdByLogin($a_parameter['username']);
						$ilUser = new ilObjUser ($user_id);

						$run = true;
						break;
				}
				break;
			case self::SERVICES_USER:
				switch ($a_event) {
					case self::CREATED_1:
					case self::CREATED_2:
					case self::UPDATED:
						$ilUser = $a_parameter['user_obj'];
						$run = true;
						break;
				}
				break;
			default:
				$run = false;
				break;
		}

		$sets = self::$mapping[$a_event];

		if ($run === true && $sets && $ilUser instanceof ilObjUser) {
			/**
			 * @var UserSetting $ilUserSetting
			 */
			foreach (UserSetting::where(array(
				'status' => UserSetting::STATUS_ACTIVE,
				$sets => true,
			))->get() as $ilUserSetting) {
				$ilUserSetting->doAssignements($ilUser);
			}
		}
	}



	//	/**
	//	 * @param $key
	//	 * @return mixed|string
	//	 * @throws ilException
	//	 */
	//	public function txt($key) {
	//		require_once('./Customizing/global/plugins/Libraries/PluginTranslator/class.sragPluginTranslator.php');
	//
	//		return sragPluginTranslator::getInstance($this)->active()->write()->txt($key);
	//	}

	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData() {
		self::dic()->database()->dropTable(UDFCheck::TABLE_NAME, false);
		self::dic()->database()->dropTable(UserSetting::TABLE_NAME, false);
		//self::dic()->database()->dropTable(usrdefUser::TABLE_NAME, false);
		//self::dic()->database()->dropTable(usrdefObj::TABLE_NAME, false);
		self::dic()->database()->dropTable(Config::TABLE_NAME, false);
	}
}
