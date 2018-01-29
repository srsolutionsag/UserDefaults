<?php
require_once('./Services/EventHandling/classes/class.ilEventHookPlugin.php');
require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/UserSetting/class.ilUserSetting.php');

/**
 * Class ilUserDefaultsPlugin
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserDefaultsPlugin extends ilEventHookPlugin {

	// Known Components
	const SERVICES_USER = 'Services/User';
	const SERVICES_AUTHENTICATION = 'Services/Authentication';
	// Known Actions
	const PLUGIN_NAME = 'UserDefaults';
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
		self::CREATED_1   => 'on_create',
		self::CREATED_2   => 'on_create',
		self::UPDATED     => 'on_update',
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
	 * Handle the event
	 *
	 * @param    string        component, e.g. "Services/User"
	 * @param    event         event, e.g. "afterUpdate"
	 * @param    array         array of event specific parameters
	 */
	public function handleEvent($a_component, $a_event, $a_parameter) {
		$run = false;
		$ilUser = null;
		switch ($a_component) {
			case self::SERVICES_AUTHENTICATION:
				switch ($a_event) {
					case self::AFTER_LOGIN:
						require_once('./Services/User/classes/class.ilObjUser.php');
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
				$sets    => true,
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
}
