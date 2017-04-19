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

	const PLUGIN_NAME = 'UserDefaults';
	const CREATED_1 = 'saveAsNew';
	const CREATED_2 = 'afterCreate';
	const UPDATED = 'afterUpdate';
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
		self::UPDATED   => 'on_update',
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
		if ($a_component == 'Services/User') {

			/**
			 * @var $ilUser ilObjUser
			 */

			$ilUser = $a_parameter['user_obj'];

			if ($ilUser instanceof ilObjUser) {
				switch ($a_event) {
					case self::CREATED_1:
					case self::CREATED_2:
						// Do Stuff
						/**
						 * @var $ilUserSetting ilUserSetting
						 */
						foreach (ilUserSetting::where(array( 'status' => ilUserSetting::STATUS_ACTIVE ))
						                      ->get() as $ilUserSetting) {
							$ilUserSetting->doAssignements($ilUser);
						}
						break;
				}
			}
		}
	}


	/**
	 * @param $key
	 * @return mixed|string
	 * @throws \ilException
	 */
		public function txt($key) {
			require_once('./Customizing/global/plugins/Libraries/PluginTranslator/class.sragPluginTranslator.php');

			return sragPluginTranslator::getInstance($this)->active()->write()->txt($key);
		}

	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}


	/**
	 * @return bool
	 */
	public static function is50() {
		$version = explode('.', ILIAS_VERSION_NUMERIC);

		return $version[0] >= 5;
	}


	/**
	 * @return bool
	 */
	public static function is51() {
		$version = explode('.', ILIAS_VERSION_NUMERIC);

		return $version[0] >= 5 && $version[1] >= 1;
	}
}
