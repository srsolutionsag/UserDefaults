<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Config\Config;
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
	const CMD_SEARCH_COURSES = 'searchCourses';
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
	 * UserSettingsGUI constructor
	 */
	public function __construct() {
		//self::plugin()->getPluginObject()->updateLanguageFiles();
		self::dic()->ctrl()->saveParameter($this, self::IDENTIFIER);
	}


	/**
	 *
	 */
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
	}


	/**
	 *
	 */
	protected function activate() {
		$ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
		$ilUserSetting->setStatus(UserSetting::STATUS_ACTIVE);
		$ilUserSetting->update();
		$this->cancel();
	}


	/**
	 *
	 */
	protected function deactivate() {
		$ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
		$ilUserSetting->setStatus(UserSetting::STATUS_INACTIVE);
		$ilUserSetting->update();
		$this->cancel();
	}


	/**
	 *
	 */
	protected function index() {
		$ilUserSettingsTableGUI = new UserSettingsTableGUI($this);
		self::dic()->mainTemplate()->setContent($ilUserSettingsTableGUI->getHTML());
	}


	/**
	 *
	 */
	protected function add() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, new UserSetting());
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	/**
	 *
	 */
	protected function create() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, new UserSetting());
		$ilUserSettingsFormGUI->setValuesByPost();
		if ($ilUserSettingsFormGUI->saveObject()) {
			ilUtil::sendSuccess(self::plugin()->translate('msg_entry_added'), true);
			self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
		}
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	/**
	 *
	 */
	protected function edit() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, UserSetting::find($_GET[self::IDENTIFIER]));
		$ilUserSettingsFormGUI->fillForm();
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	/**
	 *
	 */
	protected function update() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, UserSetting::find($_GET[self::IDENTIFIER]));
		$ilUserSettingsFormGUI->setValuesByPost();
		if ($ilUserSettingsFormGUI->saveObject()) {
			ilUtil::sendSuccess(self::plugin()->translate('msg_entry_added'), true);
			$this->cancel();
		}
		self::dic()->mainTemplate()->setContent($ilUserSettingsFormGUI->getHTML());
	}


	/**
	 *
	 */
	protected function duplicate() {
		$original = UserSetting::find($_GET[self::IDENTIFIER]);
		/** @var UserSetting $copy */
		$copy = $original->duplicate();
		$copy->setStatus(UserSetting::STATUS_INACTIVE);
		$copy->update();
		ilUtil::sendSuccess(self::plugin()->translate("msg_duplicate_successful"), true);
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	/**
	 *
	 */
	public function confirmDelete() {
		$conf = new ilConfirmationGUI();
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setHeaderText(self::plugin()->translate('msg_confirm_delete'));
		$conf->setConfirm(self::plugin()->translate('set_delete'), self::CMD_DELETE);
		$conf->setCancel(self::plugin()->translate('set_cancel'), self::CMD_INDEX);
		self::dic()->mainTemplate()->setContent($conf->getHTML());
	}


	/**
	 *
	 */
	public function delete() {
		$ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
		$ilUserSetting->delete();
		$this->cancel();
	}


	/**
	 *
	 */
	public function cancel() {
		self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, NULL);
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	/**
	 *
	 */
	protected function searchCourses() {
		$term = filter_input(INPUT_GET, "term");
		$type = filter_input(INPUT_GET, "container_type");

		$category_ref_id = Config::getCategoryRefId();

		if (!empty($category_ref_id)) {
			$courses = self::access()->getCoursesOfCategory($category_ref_id);
		} else {
			$courses = [];
		}

		$query = "SELECT obj.obj_id, obj.title
				  FROM " . usrdefObj::TABLE_NAME . " AS obj
				  LEFT JOIN object_translation AS trans ON trans.obj_id = obj.obj_id
				  JOIN object_reference AS ref ON obj.obj_id = ref.obj_id
			      WHERE obj.type = %s
			      AND (" . self::dic()->database()->like("obj.title", "text", "%%" . $term . "%%") . " OR " . self::dic()->database()
				->like("trans.title", "text", $term, "%%" . $term . "%%") . ")
				" . (!empty($courses) ? "AND " . self::dic()->database()->in("ref.ref_id", $courses, false, "integer") : "") . "
				  AND obj.title != %s
				  AND ref.deleted IS NULL
			      ORDER BY obj.title";
		$types = [ "text", "text" ];
		$values = [ $type, "__OrgUnitAdministration" ];

		$result = self::dic()->database()->queryF($query, $types, $values);

		$courses = [];
		while (($row = $result->fetchAssoc()) !== false) {
			$courses[] = [ "id" => $row["obj_id"], "text" => $row["title"] ];
		}

		self::plugin()->output($courses, false);
	}


	/**
	 *
	 */
	protected function applyFilter() {
		$tableGui = new UserSettingsTableGUI($this, self::CMD_INDEX);
		$tableGui->resetOffset(true);
		$tableGui->writeFilterToSession();
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	/**
	 *
	 */
	protected function resetFilter() {
		$tableGui = new UserSettingsTableGUI($this, self::CMD_INDEX);
		$tableGui->resetOffset();
		$tableGui->resetFilter();
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	/**
	 *
	 */
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


	/**
	 *
	 */
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


	/**
	 *
	 */
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


	/**
	 *
	 */
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


	/**
	 *
	 */
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


	/**
	 *
	 */
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
