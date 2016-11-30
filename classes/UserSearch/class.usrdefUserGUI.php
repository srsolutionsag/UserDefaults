<?php
require_once('class.usrdefUser.php');
require_once('class.usrdefUserTableGUI.php');

/**
 * Class usrdefUserGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 * @ilCtrl_IsCalledBy usrdefUserGUI : ilUserDefaultsConfigGUI
 */
class usrdefUserGUI {

	const CMD_INDEX = 'index';
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_RESET_FILTER = 'resetFilter';
	const CMD_SELECT_USER = 'selectUser';
	const IDENTIFIER = 'usr_id';
	const SESSION_ID = 'multi_assign_user_id';


	public function __construct() {
		global $ilCtrl, $tpl, $lng, $ilTabs;
		/**
		 * @var $ilCtrl    ilCtrl
		 * @var $tpl       ilTemplate
		 * @var $lng       ilLanguage
		 * @var $ilTabs    ilTabsGUI
		 */
		$this->ilCtrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->lng = $lng;
		$this->tabs = $ilTabs;
		$this->pl = ilUserDefaultsPlugin::getInstance();
		ilSession::set(self::SESSION_ID, null);
	}


	public function executeCommand() {
//		if (!usrdefAccess::hasAccess()) {
//			return false;
//		}
		$cmd = $this->ilCtrl->getCmd(self::CMD_INDEX);
		switch ($cmd) {
			case self::CMD_INDEX:
			case self::CMD_APPLY_FILTER:
			case self::CMD_RESET_FILTER:
			case self::CMD_SELECT_USER:
				// ACCESS CHECK
				$this->{$cmd}();
		}
	}


	protected function index() {
		$usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
		$this->tpl->setContent($usrdefUserTableGUI->getHTML());
	}


	protected function applyFilter() {
		$usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
		$usrdefUserTableGUI->resetOffset();
		$usrdefUserTableGUI->writeFilterToSession();
		$this->ilCtrl->redirect($this, self::CMD_INDEX);
	}


	protected function resetFilter() {
		$usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
		$usrdefUserTableGUI->resetFilter();
		$usrdefUserTableGUI->resetOffset();
		$this->ilCtrl->redirect($this, self::CMD_INDEX);
	}


	protected function selectUser() {
		$usr_ids = $_POST['id'];
		$user_objects = array();
		foreach ($usr_ids as $usr_id) {
			$user_objects[] = new ilObjUser($usr_id);
		}
		/**
		 * @var $ilUserSetting ilUserSetting
		 */
		require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/UserSetting/class.ilUserSetting.php');
		foreach (ilUserSetting::where(array( 'status' => ilUserSetting::STATUS_ACTIVE ))->get() as $ilUserSetting) {
			$ilUserSetting->doMultipleAssignements($user_objects);
		}

		$this->tpl->setContent('<pre>' . print_r($usr_ids, 1) . '</pre>');
//		ilSession::set(self::SESSION_ID, $usr_id);
//		$this->ilCtrl->redirectByClass(array( 'usrdefCourseGUI' ));
	}
}
