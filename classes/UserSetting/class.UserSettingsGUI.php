<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Config\UserDefaultsConfig;
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
	const CMD_SEARCH_LOCAL_ROLES = 'searchLocalRoles';
	const CMD_SEARCH_GLOBAL_ROLES = 'searchGlobalRoles';
	const CMD_SEARCH_COURSES = 'searchCourses';
	const CMD_SEARCH_CATEGORIES = 'searchCategories';
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
    const CMD_LINK_TO_OBJECT = 'linkToObject';


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
			case self::CMD_SEARCH_GLOBAL_ROLES:
			case self::CMD_SEARCH_LOCAL_ROLES:
			case self::CMD_SEARCH_COURSES:
			case self::CMD_SEARCH_CATEGORIES:
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
			case self::CMD_LINK_TO_OBJECT:
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
		self::output()->output($ilUserSettingsTableGUI);
	}


	/**
	 *
	 */
	protected function add() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, new UserSetting());
		self::output()->output($ilUserSettingsFormGUI);
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
		self::output()->output($ilUserSettingsFormGUI);
	}


	/**
	 *
	 */
	protected function edit() {
		$ilUserSettingsFormGUI = new UserSettingsFormGUI($this, UserSetting::find($_GET[self::IDENTIFIER]));
		$ilUserSettingsFormGUI->fillForm();
		self::output()->output($ilUserSettingsFormGUI);
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
		self::output()->output($ilUserSettingsFormGUI);
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
		self::output()->output($conf);
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
        $with_parent = (bool) filter_input(INPUT_GET, "with_parent");
        $with_members = (bool) filter_input(INPUT_GET, "with_members");
        $with_empty = (bool) filter_input(INPUT_GET, "with_empty");

		$category_ref_id = UserDefaultsConfig::getField(UserDefaultsConfig::KEY_CATEGORY_REF_ID);

		if (!empty($category_ref_id)) {
			$courses = self::ilias()->courses()->getCoursesOfCategory($category_ref_id);
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
		if ($with_empty) {
		    $courses[] = [ "id" => 0, "text" => '-'];
        }

		while (($row = $result->fetchAssoc()) !== false) {
		    $title = $row["title"];
		    if ($with_parent) {
		        $ref_id = array_shift(ilObject::_getAllReferences($row["obj_id"]));
		        $title = ilObject::_lookupTitle(ilObject::_lookupObjectId(self::dic()->repositoryTree()->getParentId($ref_id))) . ' » ' . $title;
            }
		    if ($with_members && $type == 'grp') {
                $group = new ilObjGroup($row['obj_id'], false);
                $part = ilGroupParticipants::_getInstanceByObjId($row['obj_id']);
		        $title = $title . ' (' . $part->getCountMembers() . '/' . ($group->getMaxMembers() == 0 ? '-' : $group->getMaxMembers()) . ')';
            }
			$courses[] = [ "id" => $row["obj_id"], "text" => $title ];
		}

		self::output()->outputJSON($courses);
	}

	/**
	 *
	 */
	protected function searchLocalRoles() {
		$local_roles = self::dic()->rbac()->review()->getRolesByFilter(ilRbacReview::FILTER_NOT_INTERNAL);

		$return_local_roles = array();
		foreach($local_roles as $local_role) {

			if(ilObject2::_lookupDeletedDate($local_role['parent'])) {
				continue;
			}

			$return_local_roles[] = [ "id" => $local_role["obj_id"], "text" => self::dic()->objDataCache()->lookupTitle(self::dic()->objDataCache()->lookupObjId($local_role['parent']))." >> ".$local_role["title"] ];
		}


		self::output()->outputJSON($return_local_roles);
	}


	/**
	 *
	 */
	protected function searchGlobalRoles() {
		$global_roles = self::dic()->rbac()->review()->getRolesByFilter(ilRbacReview::FILTER_ALL_GLOBAL);

		$return_global_roles = array();
		foreach($global_roles as $global_role) {

			if(ilObject2::_lookupDeletedDate($global_role['parent'])) {
				continue;
			}

			$return_global_roles[] = [ "id" => $global_role["obj_id"], "text" => $global_role["title"] ];
		}


		self::output()->outputJSON($return_global_roles);
	}


	/**
	 *
	 */
	protected function searchCategories() {
		$term = filter_input(INPUT_GET, "term");
		$type = filter_input(INPUT_GET, "container_type");

		$category_ref_id = UserDefaultsConfig::getField(UserDefaultsConfig::KEY_CATEGORY_REF_ID);

		if (!empty($category_ref_id)) {
			$categories = self::ilias()->categories()->getCategoriesOfCategory($category_ref_id);
		} else {
			$categories = [];
		}

		$query = "SELECT obj.obj_id, obj.title
				  FROM " . usrdefObj::TABLE_NAME . " AS obj
				  LEFT JOIN object_translation AS trans ON trans.obj_id = obj.obj_id
				  JOIN object_reference AS ref ON obj.obj_id = ref.obj_id
			      WHERE obj.type = %s
			      AND (" . self::dic()->database()->like("obj.title", "text", "%%" . $term . "%%") . " OR " . self::dic()->database()
				->like("trans.title", "text", $term, "%%" . $term . "%%") . ")
				" . (!empty($categories) ? "AND " . self::dic()->database()->in("ref.ref_id", $categories, false, "integer") : "") . "
				  AND obj.title != %s
				  AND ref.deleted IS NULL
			      ORDER BY obj.title";
		$types = [ "text", "text" ];
		$values = [ $type, "__OrgUnitAdministration" ];

		$result = self::dic()->database()->queryF($query, $types, $values);

		$categories = [];
		while (($row = $result->fetchAssoc()) !== false) {
			$categories[] = [ "id" => $row["obj_id"], "text" => $row["title"] ];
		}

		self::output()->outputJSON($categories);
	}

	protected function linkToObject() {
	    $obj_id = filter_input(INPUT_GET, 'obj_id', FILTER_SANITIZE_NUMBER_INT);
	    $ref_id = array_shift(ilObject::_getAllReferences($obj_id));
	    self::dic()->ctrl()->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $ref_id);
	    self::dic()->ctrl()->redirectByClass(ilRepositoryGUI::class);
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

		self::output()->output($conf);
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

		self::output()->output($conf);
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

		self::output()->output($conf);
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
