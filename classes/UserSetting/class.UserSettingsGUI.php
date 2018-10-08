<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\UserSearch\usrdefObj;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\UserSetting\UserSettingsFormGUI;
use srag\Plugins\UserDefaults\UserSetting\UserSettingsTableGUI;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class UserSettingsGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy UserSettingsGUI : ilUserDefaultsConfigGUI
 * @ilCtrl_Calls      UserSettingsGUI : ilPropertyFormGUI
 */
class UserSettingsGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const CMD_INDEX = 'configure';
	const CMD_SEARCH_COURSES = 'searchContainer';
	const CMD_CANCEL = 'cancel';
	const CMD_CREATE = 'create';
	const CMD_UPDATE = 'update';
	const CMD_ADD = 'add';
	const CMD_EDIT = 'edit';
	const CMD_CONFIRM_DELETE = 'confirmDelete';
	const CMD_DEACTIVATE = 'deactivate';
	const CMD_ACTIVATE = 'activate';
	const CMD_DELETE = 'delete';
	const CMD_DUPLICATE = 'duplicate';
	const CMD_ACTIVATE_MULTIPLE_CONFIRM = 'activateMultipleConfirm';
	const CMD_ACTIVATE_MULTIPLE = 'activateMultiple';
	const CMD_DEACTIVATE_MULTIPLE_CONFIRM = 'deactivateMultipleConfirm';
	const CMD_DEACTIVATE_MULTIPLE = 'deactivateMultiple';
	const CMD_DELETE_MULTIPLE_CONFIRM = 'deleteMultipleConfirm';
	const CMD_DELETE_MULTIPLE = 'deleteMultiple';
	const IDENTIFIER = 'set_id';


	/**
	 * @param $parent_gui
	 */
	public function __construct($parent_gui) {
		//		self::plugin()->getPluginObject()->updateLanguageFiles();
		self::dic()->ctrl()->saveParameter($this, self::IDENTIFIER);
	}


	public function executeCommand() {
		$cmd = self::dic()->ctrl()->getCmd(self::CMD_INDEX);
		$cmdClass = self::dic()->ctrl()->getCmdClass();

		switch ($cmd) {
			case self::CMD_INDEX:
				$this->index();
				break;
			case self::CMD_SEARCH_COURSES:
			case self::CMD_CANCEL:
			case self::CMD_CREATE:
			case self::CMD_UPDATE:
			case self::CMD_ADD:
			case self::CMD_EDIT:
			case self::CMD_ACTIVATE:
			case self::CMD_DEACTIVATE:
			case self::CMD_CONFIRM_DELETE:
			case self::CMD_DELETE:
			case self::CMD_DUPLICATE:
			case self::CMD_ACTIVATE_MULTIPLE_CONFIRM:
			case self::CMD_ACTIVATE_MULTIPLE:
			case self::CMD_DEACTIVATE_MULTIPLE_CONFIRM:
			case self::CMD_DEACTIVATE_MULTIPLE:
			case self::CMD_DELETE_MULTIPLE_CONFIRM:
			case self::CMD_DELETE_MULTIPLE:
				$this->{$cmd}();
				break;
		}

		return true;
	}


	protected function activate() {
		$ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
		$ilUserSetting->setStatus(UserSetting::STATUS_ACTIVE);
		$ilUserSetting->update();
		$this->cancel();
	}


	protected function deactivate() {
		$ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
		$ilUserSetting->setStatus(UserSetting::STATUS_INACTIVE);
		$ilUserSetting->update();
		$this->cancel();
	}


	protected function index() {
		$ilUserSettingsTableGUI = new UserSettingsTableGUI($this);
		self::dic()->mainTemplate()->setContent($ilUserSettingsTableGUI->getHTML());
	}


	protected function add() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, new UserSetting());
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	protected function create() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, new UserSetting());
		$ilUserSettingsFormGUI->setValuesByPost();
		if ($ilUserSettingsFormGUI->saveObject()) {
			ilUtil::sendSuccess(self::plugin()->translate('msg_entry_added'), true);
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		}
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	protected function edit() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, UserSetting::find($_GET[self::IDENTIFIER]));
		$ilUserSettingsFormGUI->fillForm();
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	protected function update() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, UserSetting::find($_GET[self::IDENTIFIER]));
		$ilUserSettingsFormGUI->setValuesByPost();
		if ($ilUserSettingsFormGUI->saveObject()) {
			ilUtil::sendSuccess(self::plugin()->translate('msg_entry_added'), true);
			$this->cancel();
		}
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	protected function duplicate() {
		$original = UserSetting::find($_GET[self::IDENTIFIER]);
		/** @var UserSetting $copy */
		$copy = $original->duplicate();
		$copy->setStatus(UserSetting::STATUS_INACTIVE);
		$copy->update();
		ilUtil::sendSuccess(self::plugin()->translate("msg_duplicate_successful"), true);
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	public function confirmDelete() {
		$conf = new ilConfirmationGUI();
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setHeaderText(self::plugin()->translate('msg_confirm_delete'));
		$conf->setConfirm(self::plugin()->translate('set_delete'), self::CMD_DELETE);
		$conf->setCancel(self::plugin()->translate('set_cancel'), self::CMD_INDEX);
		self::dic()->mainTemplate()->setContent($conf->getHTML());
	}


	public function delete() {
		$ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
		$ilUserSetting->delete();
		$this->cancel();
	}


	public function cancel() {
		self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, NULL);
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function searchContainer() {
		$term = self::dic()->database()->quote('%' . $_GET['term'] . '%', 'text');
		$type = self::dic()->database()->quote($_GET['container_type'], 'text');

		$query = "SELECT obj.obj_id, obj.title
				FROM " . usrdefObj::TABLE_NAME . " obj
				 LEFT JOIN object_translation trans ON trans.obj_id = obj.obj_id
				 JOIN object_reference ref ON obj.obj_id = ref.obj_id
			 WHERE obj.type = $type AND
				 (obj.title LIKE $term OR trans.title LIKE $term)
				 AND ref.deleted IS NULL
			 ORDER BY  obj.title";

		$res = self::dic()->database()->query($query);
		$result = array();
		while ($row = self::dic()->database()->fetchAssoc($res)) {
			if ($row['title'] != "__OrgUnitAdministration") {
				$result[] = array( "id" => $row['obj_id'], "text" => $row['title'] );
			}
		}
		echo json_encode($result);
		exit;
	}


	protected function applyFilter() {
		$tableGui = new UserSettingsTableGUI($this, self::CMD_INDEX);
		$tableGui->resetOffset(true);
		$tableGui->writeFilterToSession();
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function resetFilter() {
		$tableGui = new UserSettingsTableGUI($this, self::CMD_INDEX);
		$tableGui->resetOffset();
		$tableGui->resetFilter();
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function activateMultipleConfirm() {
		$setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		if (!is_array($setting_select) || count($setting_select) === 0) {
			// No settings selected
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		};

		$conf = new ilConfirmationGUI();
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setHeaderText(self::plugin()->translate('msg_confirm_activate_multiple'));
		$conf->setConfirm(self::plugin()->translate('set_activate'), self::CMD_ACTIVATE_MULTIPLE);
		$conf->setCancel(self::plugin()->translate('set_cancel'), self::CMD_INDEX);

		foreach ($setting_select as $setting_id) {
			$conf->addItem("setting_select[]", $setting_id, UserSetting::find($setting_id)->getTitle());
		}

		self::dic()->mainTemplate()->setContent($conf->getHTML());
	}


	protected function activateMultiple() {
		$setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		if (!is_array($setting_select) || count($setting_select) === 0) {
			// No settings selected
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		};

		foreach ($setting_select as $setting_id) {
			$ilUserSetting = UserSetting::find($setting_id);
			$ilUserSetting->setStatus(UserSetting::STATUS_ACTIVE);
			$ilUserSetting->update();
		}

		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function deactivateMultipleConfirm() {
		$setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		if (!is_array($setting_select) || count($setting_select) === 0) {
			// No settings selected
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		};

		$conf = new ilConfirmationGUI();
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setHeaderText(self::plugin()->translate('msg_confirm_deactivate_multiple'));
		$conf->setConfirm(self::plugin()->translate('set_deactivate'), self::CMD_DEACTIVATE_MULTIPLE);
		$conf->setCancel(self::plugin()->translate('set_cancel'), self::CMD_INDEX);

		foreach ($setting_select as $setting_id) {
			$conf->addItem("setting_select[]", $setting_id, UserSetting::find($setting_id)->getTitle());
		}

		self::dic()->mainTemplate()->setContent($conf->getHTML());
	}


	protected function deactivateMultiple() {
		$setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		if (!is_array($setting_select) || count($setting_select) === 0) {
			// No settings selected
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		};

		foreach ($setting_select as $setting_id) {
			$ilUserSetting = UserSetting::find($setting_id);
			$ilUserSetting->setStatus(UserSetting::STATUS_INACTIVE);
			$ilUserSetting->update();
		}

		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function deleteMultipleConfirm() {
		$setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		if (!is_array($setting_select) || count($setting_select) === 0) {
			// No settings selected
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		};

		$conf = new ilConfirmationGUI();
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setHeaderText(self::plugin()->translate('msg_confirm_delete_multiple'));
		$conf->setConfirm(self::plugin()->translate('set_delete'), self::CMD_DELETE_MULTIPLE);
		$conf->setCancel(self::plugin()->translate('set_cancel'), self::CMD_INDEX);

		foreach ($setting_select as $setting_id) {
			$conf->addItem("setting_select[]", $setting_id, UserSetting::find($setting_id)->getTitle());
		}

		self::dic()->mainTemplate()->setContent($conf->getHTML());
	}


	protected function deleteMultiple() {
		$setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
		if (!is_array($setting_select) || count($setting_select) === 0) {
			// No settings selected
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		};

		foreach ($setting_select as $setting_id) {
			$ilUserSetting = UserSetting::find($setting_id);
			$ilUserSetting->delete();
		}

		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}
}
