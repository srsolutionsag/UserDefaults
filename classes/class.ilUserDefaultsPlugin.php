<?php

require_once __DIR__ . "/../vendor/autoload.php";

/**
 * Class ilUserDefaultsPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserDefaultsPlugin extends ilEventHookPlugin {

	const PLUGIN_ID = 'usrdef';
	const PLUGIN_NAME = 'UserDefaults';
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
	 * @var ilDB
	 */
	protected $db;


	/**
	 *
	 */
	public function __construct() {
		parent::__construct();

		global $DIC;

		$this->db = $DIC->database();
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
			 * @var $ilUserSetting ilUserSetting
			 */
			foreach (ilUserSetting::where(array(
				'status' => ilUserSetting::STATUS_ACTIVE,
				$sets => true,
			))->get() as $ilUserSetting) {
				$ilUserSetting->doAssignements($ilUser);
			}
		}
	}



	//	/**
	//	 * @param $key
	//	 * @return mixed|string
	//	 * @throws \ilException
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
	 * @return bool
	 */
	protected function beforeUninstall() {
		$this->db->dropTable(ilUDFCheck::TABLE_NAME, false);
		$this->db->dropTable(ilUserSetting::TABLE_NAME, false);
		//$this->db->dropTable(usrdefUser::TABLE_NAME, false);
		//$this->db->dropTable(usrdefObj::TABLE_NAME, false);

		return true;
	}
}
