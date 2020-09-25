<?php

require_once __DIR__ . "/../vendor/autoload.php";

use ILIAS\GlobalScreen\Scope\MainMenu\Provider\AbstractStaticPluginMainMenuProvider;
use srag\Plugins\UserDefaults\Config\Config;
use srag\Plugins\UserDefaults\Menu\Menu;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheck;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use srag\RemovePluginDataConfirm\UserDefaults\PluginUninstallTrait;

/**
 * Class ilUserDefaultsPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserDefaultsPlugin extends ilEventHookPlugin {

	use PluginUninstallTrait;
	use UserDefaultsTrait;
	const PLUGIN_ID = 'usrdef';
	const PLUGIN_NAME = 'UserDefaults';
	const PLUGIN_CLASS_NAME = self::class;
	const REMOVE_PLUGIN_DATA_CONFIRM_CLASS_NAME = usrdefRemoveDataConfirm::class;
	// Known Components
	const SERVICES_USER = 'Services/User';
	const SERVICES_OBJECT = 'Services/Object';
	const SERVICES_AUTHENTICATION = 'Services/Authentication';
	const MODULES_ORGUNITS = 'Modules/OrgUnit';
	// Known Actions
	const CREATED_1 = 'saveAsNew';
	const CREATED_2 = 'afterCreate';
	const UPDATED = 'afterUpdate';
	const UPDATE = 'update';
	const AFTER_LOGIN = 'afterLogin';
	const ASSIGN_USER_TO_POSITION = 'assignUserToPosition';
	const REMOVE_USER_FROM_POSITION = 'removeUserFromPosition';
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
		self::UPDATE => 'on_update',
		self::AFTER_LOGIN => 'on_update',
		self::ASSIGN_USER_TO_POSITION => 'on_update',
		self::REMOVE_USER_FROM_POSITION => 'on_update'
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
		$user = NULL;
		switch ($a_component) {
			case self::SERVICES_AUTHENTICATION:
				switch ($a_event) {
					case self::AFTER_LOGIN:
						$user_id = ilObjUser::getUserIdByLogin($a_parameter['username']);
						$user = new ilObjUser($user_id);

						$run = true;
						break;
				}
				break;
            case self::SERVICES_OBJECT:
                switch ($a_event) {
                    case self::UPDATE:
                        if ($a_parameter['obj_type'] == 'usr') {
                            $user = new ilObjUser($a_parameter['obj_id']);
                            $run = true;
                        }
                        break;
                }
                break;
            case self::SERVICES_USER:
				switch ($a_event) {
					case self::CREATED_1:
					case self::CREATED_2:
					case self::UPDATED:
					case self::UPDATE:
						$user = $a_parameter['user_obj'];
						$run = true;
						break;
				}
				break;
			case self::MODULES_ORGUNITS:
				switch ($a_event) {
					case self::ASSIGN_USER_TO_POSITION:
					case self::REMOVE_USER_FROM_POSITION:
						$user = new ilObjUser($a_parameter['user_id']);
						$run = true;
						break;
				}
				break;
			default:
				$run = false;
				break;
		}

		$sets = self::$mapping[$a_event];


		if ($run === true && $sets && $user instanceof ilObjUser) {
			/**
			 * @var UserSetting $ilUserSetting
			 */
			foreach (UserSetting::where(array(
				'status' => UserSetting::STATUS_ACTIVE,
				$sets => true,
			))->get() as $ilUserSetting) {
				$ilUserSetting->doAssignements($user);
			}
		}
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @inheritdoc
	 */
	public function promoteGlobalScreenProvider(): AbstractStaticPluginMainMenuProvider {
		return new Menu(self::dic()->dic(), $this);
	}


	/**
	 * @inheritdoc
	 */
	protected function deleteData()/*: void*/ {
		self::dic()->database()->dropTable(UDFCheckOld::TABLE_NAME, false);
		foreach (UDFCheck::$class_names as $class) {
			self::dic()->database()->dropTable($class::TABLE_NAME, false);
		}
		self::dic()->database()->dropTable(UserSetting::TABLE_NAME, false);
		//self::dic()->database()->dropTable(usrdefUser::TABLE_NAME, false);
		//self::dic()->database()->dropTable(usrdefObj::TABLE_NAME, false);
		self::dic()->database()->dropTable(Config::TABLE_NAME, false);
	}


    /**
     * @inheritDoc
     */
	protected function shouldUseOneUpdateStepOnly() : bool
    {
        return false;
    }
}
