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

	/**
	 * @var
	 */
	protected static $instance;


	/**
	 * @return ilUserDefaultsPlugin
	 */
	public static function getInstance() {
		if (! isset(self::$instance)) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	const PLUGIN_NAME = 'UserDefaults';


	/**
	 * Handle the event
	 *
	 * @param    string        component, e.g. "Services/User"
	 * @param    event         event, e.g. "afterUpdate"
	 * @param    array         array of event specific parameters
	 */
	public function handleEvent($a_component, $a_event, $a_parameter) {

		if ($a_component == 'Modules/Course' AND $a_event == 'update') {
			global $ilUser;
			//			ilUDFCheck::installDB();
			//			ilUserSetting::installDB();
			$ilUserSetting = new ilUserSetting();
			$ilUserSetting->setTitle('Stuzdienanwärter');
			$ilUserSetting->setGlobalRole(4);
			$ilUserSetting->setAssignedCourses(array( 73 ));
			$ilUserSetting->setStatus(ilUserSetting::STATUS_ACTIVE);
			//			$ilUserSetting->create();
			/**
			 * @var $ilUserSetting ilUserSetting
			 */
			foreach (ilUserSetting::where(array( 'status' => ilUserSetting::STATUS_ACTIVE ))->get() as $ilUserSetting) {
				$ilUserSetting->doAssignements();
			}
		}

		if ($a_component == 'Services/User' AND $a_event == 'saveAsNew') {
			/**
			 * @var $ilUser ilObjUser
			 */

			$ilUser = $a_parameter['user_obj'];
			if ($ilUser instanceof ilObjUser) {
				// Do Stuff
				/**
				 * @var $ilUserDefinedFields ilUserDefinedFields
				 */
				//				$ilUserDefinedFields = ilUserDefinedFields::_getInstance();
				//				var_dump($ilUserDefinedFields); // FSX

				$ilUser->readUserDefinedFields();

				echo '<pre>' . print_r($ilUser->user_defined_data, 1) . '</pre>';

				exit;
			}
		}
	}


	/**
	 * @return string
	 */
	public function getPluginName() {
		return self::PLUGIN_NAME;
	}
}

?>
