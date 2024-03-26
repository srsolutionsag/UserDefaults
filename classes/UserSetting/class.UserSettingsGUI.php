<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\UserDefaults\Adapters\Api;

use srag\Plugins\UserDefaults\UserDefaultsApi;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * @ilCtrl_IsCalledBy UserSettingsGUI : ilUserDefaultsConfigGUI
 * @ilCtrl_Calls      UserSettingsGUI : ilPropertyFormGUI
 */
class UserSettingsGUI
{
    use UserDefaultsTrait;

    const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    const CMD_INDEX = 'configure';

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
    private ilCtrl $ctrl;
    private ilUserDefaultsPlugin $pl;
    private ilGlobalTemplateInterface $tpl;
    private \ILIAS\DI\UIServices $ui;
    private ilDBInterface $db;
    private ilTree $repositoryTree;
    private \ILIAS\DI\RBACServices $rbac;
    private ilObjectDataCache $objDataCache;
    private UserDefaultsApi $userDefaultsApi;


    /**
     * UserSettingsGUI constructor
     * @throws ilCtrlException
     */
    public function __construct()
    {
        global $DIC;
        //is access granted
        if(!ilUserDefaultsPlugin::grantAccess()) {
            echo "no Settings Permission";
            exit;
        };


        $this->ctrl = $DIC->ctrl();
        $this->ui = $DIC->ui();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilUserDefaultsPlugin::getInstance();
        $this->db = $DIC->database();
        $this->repositoryTree = $DIC->repositoryTree();
        $this->rbac = $DIC->rbac();
        $this->objDataCache = $DIC["ilObjDataCache"];
        $this->ctrl->saveParameter($this, self::IDENTIFIER);

        $this->userDefaultsApi = UserDefaultsApi::new();
    }


    public function executeCommand(): void
    {
        $cmd = $this->ctrl->getCmd(self::CMD_INDEX);
        switch ($cmd) {
            case self::CMD_INDEX:
                $this->index();
                break;
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
     * @throws ilCtrlException
     */
    protected function activate(): void
    {
        $ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
        $ilUserSetting->setStatus(UserSetting::STATUS_ACTIVE);
        $ilUserSetting->update();
        $this->cancel();
    }

    /**
     * @throws ilCtrlException
     */
    protected function deactivate(): void
    {
        $ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
        $ilUserSetting->setStatus(UserSetting::STATUS_INACTIVE);
        $ilUserSetting->update();
        $this->cancel();
    }

    /**
     * @throws ilException
     * @throws ilCtrlException
     */
    protected function index(): void
    {
        $this->userDefaultsApi->assignmentProcesses->renderTable($this);
    }


    protected function add(): void
    {
        $this->userDefaultsApi->assignmentProcesses->renderForm($this);
    }

    protected function create(): void
    {
        $onSuccess = function () {
            $this->tpl->setOnScreenMessage('success', $this->pl->txt('msg_entry_added'), true);
            $this->ctrl->redirect($this, self::CMD_INDEX);
        };
        $this->userDefaultsApi->assignmentProcesses->handleFormSubmission($this, null, $onSuccess);
    }

    protected function edit(): void
    {
       $this->userDefaultsApi->assignmentProcesses->renderForm($this, $_GET[self::IDENTIFIER]);
    }

    protected function update(): void
    {
        $onSuccess = function () {
            $this->tpl->setOnScreenMessage('success', $this->pl->txt('msg_entry_added'), true);
            $this->ctrl->redirect($this, self::CMD_INDEX);
        };
       $this->userDefaultsApi->assignmentProcesses->handleFormSubmission($this, $_GET[self::IDENTIFIER], $onSuccess);
    }

    /**
     * @throws ilCtrlException
     */
    protected function duplicate(): void
    {
        $original = UserSetting::find($_GET[self::IDENTIFIER]);
        $copy = $original->duplicate();
        $copy->setStatus(UserSetting::STATUS_INACTIVE);
        $copy->update();
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('msg_duplicate_successful'), true);
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /**
     * @throws ilCtrlException
     */
    public function confirmDelete(): void
    {
        $conf = new ilConfirmationGUI();
        $conf->setFormAction($this->ctrl->getFormAction($this));
        $conf->setHeaderText($this->pl->txt('msg_confirm_delete'));
        $conf->setConfirm($this->pl->txt('set_delete'), self::CMD_DELETE);
        $conf->setCancel($this->pl->txt('set_cancel'), self::CMD_INDEX);
        $this->ui->mainTemplate()->setContent($conf->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    public function delete(): void
    {
        $ilUserSetting = UserSetting::find($_GET[self::IDENTIFIER]);
        $ilUserSetting->delete();
        $this->cancel();
    }

    /**
     * @throws ilCtrlException
     */
    public function cancel(): void
    {
        $this->ctrl->setParameter($this, self::IDENTIFIER, NULL);
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /*

    protected function searchGroups(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode(new stdClass());
        exit;
        $term = filter_input(INPUT_GET, "term");
        $type = filter_input(INPUT_GET, "container_type");
        $with_parent = (bool)filter_input(INPUT_GET, "with_parent");
        $with_members = (bool)filter_input(INPUT_GET, "with_members");
        $with_empty = (bool)filter_input(INPUT_GET, "with_empty");

        $userDefaultsConfig = UserDefaultsConfig::findOrGetInstance(UserDefaultsConfig::KEY_CATEGORY_REF_ID);
        if (!empty($userDefaultsConfig->getValue())) {
            $groups = $this->repositoryTree->getSubTree($this->repositoryTree->getNodeData($userDefaultsConfig->getValue()), false, ["grp"]);
        } else {
            $groups = [];
        }
        $query = "SELECT obj.obj_id, obj.title
				  FROM " . usrdefObj::TABLE_NAME . " AS obj
				  LEFT JOIN object_translation AS trans ON trans.obj_id = obj.obj_id
				  JOIN object_reference AS ref ON obj.obj_id = ref.obj_id
			      WHERE obj.type = %s
			      AND (" . $this->db->like("obj.title", "text", "%%" . $term . "%%") . " OR " . $this->db
                ->like("trans.title", "text", $term, "%%" . $term . "%%") . ")
				" . (!empty($groups) ? "AND " . $this->db->in("ref.ref_id", $groups, false, "integer") : "") . "
				  AND obj.title != %s
				  AND ref.deleted IS NULL
			      ORDER BY obj.title";
        $types = ["text", "text"];
        $values = [$type, "__OrgUnitAdministration"];

        $result = $this->db->queryF($query, $types, $values);

        $courses = [];
        if ($with_empty) {
            $courses[] = ["id" => 0, "text" => '-'];
        }
        $rows = $this->db->fetchAll($result);
        foreach ($rows as $row) {
            $title = $row["title"];
            if ($with_parent) {
                $allReferences = ilObject::_getAllReferences($row["obj_id"]);
                $ref_id = array_shift($allReferences);
                $title = ilObject::_lookupTitle(ilObject::_lookupObjectId($this->repositoryTree->getParentId($ref_id))) . ' » ' . $title;
            }
            if ($with_members && $type == 'grp') {
                $group = new ilObjGroup($row['obj_id'], false);
                $part = ilGroupParticipants::_getInstanceByObjId($row['obj_id']);
                $title = $title . ' (' . $part->getCountMembers() . '/' . ($group->getMaxMembers() == 0 ? '-' : $group->getMaxMembers()) . ')';
            }
            $courses[] = ["id" => $row["obj_id"], "text" => $title];
        }
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($courses);
        exit;
    }*/


    /**
     * @throws ilException
     */
    /*
    protected function searchCategories(): void
    {
        $term = filter_input(INPUT_GET, "term");
        $type = filter_input(INPUT_GET, "container_type");

        $category_ref_id = UserDefaultsConfig::getField(UserDefaultsConfig::KEY_CATEGORY_REF_ID);

        if (!empty($category_ref_id)) {
            $categories = $this->repositoryTree->getSubTree($this->repositoryTree->getNodeData($category_ref_id), false, ['cat']);
        } else {
            $categories = [];
        }

        $query = "SELECT obj.obj_id, obj.title
				  FROM " . usrdefObj::TABLE_NAME . " AS obj
				  LEFT JOIN object_translation AS trans ON trans.obj_id = obj.obj_id
				  JOIN object_reference AS ref ON obj.obj_id = ref.obj_id
			      WHERE obj.type = %s
			      AND (" . $this->db->like("obj.title", "text", "%%" . $term . "%%") . " OR " . $this->db
                ->like("trans.title", "text", $term, "%%" . $term . "%%") . ")
				" . (!empty($categories) ? "AND " . $this->db->in("ref.ref_id", $categories, false, "integer") : "") . "
				  AND obj.title != %s
				  AND ref.deleted IS NULL
			      ORDER BY obj.title";
        $types = ["text", "text"];
        $values = [$type, "__OrgUnitAdministration"];

        $result = $this->db->queryF($query, $types, $values);

        $categories = [];
        while (($row = $result->fetchAssoc()) !== false) {
            $categories[] = ["id" => $row["obj_id"], "text" => $row["title"]];
        }
        header("Content-Type: application/json; charset=utf-8");
        echo json_encode($categories);
        exit;
    }*/

    /**
     * @throws ilCtrlException
     */
    protected function linkToObject(): void
    {
        $obj_id = filter_input(INPUT_GET, 'obj_id', FILTER_SANITIZE_NUMBER_INT);
        $allReferences = ilObject::_getAllReferences($obj_id);
        $ref_id = array_shift($allReferences);
        $this->ctrl->setParameterByClass(ilRepositoryGUI::class, 'ref_id', $ref_id);
        $this->ctrl->redirectByClass(ilRepositoryGUI::class);
    }


    /**
     * @throws ilCtrlException
     */
    protected function activateMultipleConfirm(): void
    {
        $setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (!is_array($setting_select) || count($setting_select) === 0) {
            // No settings selected
            $this->ctrl->redirect($this, self::CMD_INDEX);
        };

        $conf = new ilConfirmationGUI();
        $conf->setFormAction($this->ctrl->getFormAction($this));
        $conf->setHeaderText($this->pl->txt('msg_confirm_activate_multiple'));
        $conf->setConfirm($this->pl->txt('set_activate'), self::CMD_ACTIVATE_MULTIPLE);
        $conf->setCancel($this->pl->txt('set_cancel'), self::CMD_INDEX);

        foreach ($setting_select as $setting_id) {
            $conf->addItem("setting_select[]", $setting_id, UserSetting::find($setting_id)->getTitle());
        }
        $this->ui->mainTemplate()->setContent($conf->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    protected function activateMultiple(): void
    {
        $setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (!is_array($setting_select) || count($setting_select) === 0) {
            // No settings selected
            $this->ctrl->redirect($this, self::CMD_INDEX);
        };

        foreach ($setting_select as $setting_id) {
            $ilUserSetting = UserSetting::find($setting_id);
            $ilUserSetting->setStatus(UserSetting::STATUS_ACTIVE);
            $ilUserSetting->update();
        }
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /**
     * @throws ilCtrlException
     */
    protected function deactivateMultipleConfirm(): void
    {
        $setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (!is_array($setting_select) || count($setting_select) === 0) {
            // No settings selected
            $this->ctrl->redirect($this, self::CMD_INDEX);
        };

        $conf = new ilConfirmationGUI();
        $conf->setFormAction($this->ctrl->getFormAction($this));
        $conf->setHeaderText($this->pl->txt('msg_confirm_deactivate_multiple'));
        $conf->setConfirm($this->pl->txt('set_deactivate'), self::CMD_DEACTIVATE_MULTIPLE);
        $conf->setCancel($this->pl->txt('set_cancel'), self::CMD_INDEX);

        foreach ($setting_select as $setting_id) {
            $conf->addItem("setting_select[]", $setting_id, UserSetting::find($setting_id)->getTitle());
        }
        $this->ui->mainTemplate()->setContent($conf->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    protected function deactivateMultiple(): void
    {
        $setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (!is_array($setting_select) || count($setting_select) === 0) {
            // No settings selected
            $this->ctrl->redirect($this, self::CMD_INDEX);
        };
        foreach ($setting_select as $setting_id) {
            $ilUserSetting = UserSetting::find($setting_id);
            $ilUserSetting->setStatus(UserSetting::STATUS_INACTIVE);
            $ilUserSetting->update();
        }
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /**
     * @throws ilCtrlException
     */
    protected function deleteMultipleConfirm(): void
    {
        $setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (!is_array($setting_select) || count($setting_select) === 0) {
            // No settings selected
            $this->ctrl->redirect($this, self::CMD_INDEX);
        }
        $conf = new ilConfirmationGUI();
        $conf->setFormAction($this->ctrl->getFormAction($this));
        $conf->setHeaderText($this->pl->txt('msg_confirm_delete_multiple'));
        $conf->setConfirm($this->pl->txt('set_delete'), self::CMD_DELETE_MULTIPLE);
        $conf->setCancel($this->pl->txt('set_cancel'), self::CMD_INDEX);
        foreach ($setting_select as $setting_id) {
            $conf->addItem("setting_select[]", $setting_id, UserSetting::find($setting_id)->getTitle());
        }
        $this->ui->mainTemplate()->setContent($conf->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    protected function deleteMultiple(): void
    {
        $setting_select = filter_input(INPUT_POST, 'setting_select', FILTER_DEFAULT, FILTER_FORCE_ARRAY);
        if (!is_array($setting_select) || count($setting_select) === 0) {
            // No settings selected
            $this->ctrl->redirect($this, self::CMD_INDEX);
        };
        foreach ($setting_select as $setting_id) {
            $ilUserSetting = UserSetting::find($setting_id);
            $ilUserSetting->delete();
        }
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }
}
