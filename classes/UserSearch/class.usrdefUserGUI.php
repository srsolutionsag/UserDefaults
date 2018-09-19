<?php

require_once __DIR__ . "/../../vendor/autoload.php";
use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;

/**
 * Class usrdefUserGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 * @ilCtrl_IsCalledBy usrdefUserGUI : ilUserDefaultsConfigGUI
 * @ilCtrl_Calls      usrdefUserGUI : ilpropertyformgui
 */
class usrdefUserGUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const CMD_INDEX = 'index';
	const CMD_APPLY_FILTER = 'applyFilter';
	const CMD_RESET_FILTER = 'resetFilter';
	const CMD_SELECT_USER = 'selectUser';
	const CMD_CONFIRM = 'confirmSelectUser';
	const IDENTIFIER = 'usr_id';
	const SESSION_ID = 'multi_assign_user_id';


	public function __construct() {
		ilSession::set(self::SESSION_ID, NULL);
	}


	public function executeCommand() {
		$next = self::dic()->ctrl()->getNextClass();
		$cmd = self::dic()->ctrl()->getCmd(self::CMD_INDEX);
		switch ($next) {
			case strtolower(ilPropertyFormGUI::class):
				$usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
				switch ($_GET['exp_cont']) {
					case 'il_expl2_jstree_cont_rep_exp_sel_repo':
						$usrdefUserTableGUI->getCrsSelectorGUI()->handleExplorerCommand();
						break;
					case 'il_expl2_jstree_cont_rep_exp_sel_orgu':
						$usrdefUserTableGUI->getOrguSelectorGUI()->handleExplorerCommand();
						break;
				}

				break;
			default:
				switch ($cmd) {
					case self::CMD_INDEX:
					case self::CMD_APPLY_FILTER:
					case self::CMD_RESET_FILTER:
					case self::CMD_SELECT_USER:
						// ACCESS CHECK
						$this->{$cmd}();
				}
				break;
		}
	}


	protected function index() {
		$usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
		self::dic()->template()->setContent($usrdefUserTableGUI->getHTML());
	}


	protected function applyFilter() {
		$usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
		$usrdefUserTableGUI->resetOffset();
		$usrdefUserTableGUI->writeFilterToSession();
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function resetFilter() {
		$usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
		$usrdefUserTableGUI->resetFilter();
		$usrdefUserTableGUI->resetOffset();
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function confirmSelectUser() {
		// Optinal
	}


	protected function selectUser() {
		$usr_ids = $_POST['id'];
		$user_objects = array();
		if (count($usr_ids) == 0 || !is_array($usr_ids)) {
			ilUtil::sendFailure(self::plugin()->translate('msg_no_users_selected'), true);
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		}
		foreach ($usr_ids as $usr_id) {
			$user_objects[] = new ilObjUser($usr_id);
		}
		/**
		 * @var UserSetting $ilUserSetting
		 */
		foreach (UserSetting::where(array(
			'status' => UserSetting::STATUS_ACTIVE,
			'on_manual' => true,
		))->get() as $ilUserSetting) {
			$ilUserSetting->doMultipleAssignements($user_objects);
		}

		ilUtil::sendSuccess(self::plugin()->translate("userdef_users_assigned", "", [ count($usr_ids) ]), true);
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}
}
