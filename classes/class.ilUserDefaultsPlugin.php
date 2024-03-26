<?php

require_once __DIR__ . "/../vendor/autoload.php";

use srag\Plugins\UserDefaults\Config\UserDefaultsConfig;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheck;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheckOld;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class ilUserDefaultsPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserDefaultsPlugin extends ilEventHookPlugin {

	use UserDefaultsTrait;
	const PLUGIN_ID = 'usrdef';
	const PLUGIN_NAME = 'UserDefaults';
	const PLUGIN_CLASS_NAME = self::class;
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
	protected static array $mapping = array(
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
	public static function getInstance(): ilUserDefaultsPlugin
    {
		if (!isset(self::$instance)) {
            global $DIC;

            /** @var $component_factory ilComponentFactory */
            $component_factory = $DIC['component.factory'];
            /** @var $plugin ilUserDefaultsPlugin */
            $plugin  = $component_factory->getPlugin(ilUserDefaultsPlugin::PLUGIN_ID);

			self::$instance = $plugin;
		}

		return self::$instance;
	}
    public function __construct(
        ilDBInterface $db,
        ilComponentRepositoryWrite $component_repository,
        string $id
    ) {
        parent::__construct($db, $component_repository, $id);
	}

    public function getPrefix(): string {
        return "evnt_evhk_usrdef";
    }


	public function handleEvent(string $a_component,
                                string $a_event,
                                array $a_parameter): void
    {

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
						$user = new ilObjUser($a_parameter['usr_id']);
						$run = true;
						break;
				}
				break;
			default:
				$run = false;
				break;
		}

        if(array_key_exists($a_event, self::$mapping)) {
            $sets = self::$mapping[$a_event];
        }


        // adding orgunits emits an event and ends up in a loop
		if ($run === true && $sets && $user instanceof ilObjUser && !str_contains($a_component, "Modules/OrgUnit")) {
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

	public function getPluginName(): string
    {
		return self::PLUGIN_NAME;
	}


	protected function afterUninstall(): void {
        $this->db->dropTable(UDFCheckOld::TABLE_NAME, false);
		foreach (UDFCheck::$class_names as $class) {
            $this->db->dropTable($class::TABLE_NAME, false);
		}
        $this->db->dropTable(UserSetting::TABLE_NAME, false);
		//self::dic()->database()->dropTable(usrdefUser::TABLE_NAME, false);
		//self::dic()->database()->dropTable(usrdefObj::TABLE_NAME, false);
        $this->db->dropTable(UserDefaultsConfig::TABLE_NAME, false);
	}

    public function getImagePath(string $imageName): string {
        return $this->getDirectory()."/templates/images/".$imageName;
    }

    	public static function grantAccess():bool {
	    global $DIC;
	 	// check if user is allowed to configure UserDefauts
		// since major parts of the plugin assign roles to users the capability to assign roles in useradministration is checked
		// write would check if user can edit settings
	    return ($DIC->rbac()->system()->checkAccess("edit_roleassignment",USER_FOLDER_ID));
    	}
    /**
     * @inheritDoc
     */
	protected function shouldUseOneUpdateStepOnly() : bool
    {
        return false;
    }
}
