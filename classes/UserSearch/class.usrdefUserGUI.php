<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * @ilCtrl_IsCalledBy usrdefUserGUI : ilUserDefaultsConfigGUI
 * @ilCtrl_Calls      usrdefUserGUI : ilpropertyformgui
 */
class usrdefUserGUI
{
    use UserDefaultsTrait;

    const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    const CMD_INDEX = 'index';
    const CMD_APPLY_FILTER = 'applyFilter';
    const CMD_RESET_FILTER = 'resetFilter';
    const CMD_SELECT_USER = 'selectUser';
    const CMD_CONFIRM = 'confirmSelectUser';
    const IDENTIFIER = 'usr_id';
    const SESSION_ID = 'multi_assign_user_id';
    private ilCtrl $ctrl;
    private ilUserDefaultsPlugin $pl;
    private ilGlobalTemplateInterface $tpl;
    private \ILIAS\DI\UIServices $ui;

    /**
     * usrdefUserGUI constructor
     */
    public function __construct()
    {
        global $DIC;
        //Check Access
        if(!ilUserDefaultsPlugin::grantAccess()) {
            echo "no Search Permission";
            exit;
        };

        $this->ctrl = $DIC->ctrl();
        $this->ui = $DIC->ui();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilUserDefaultsPlugin::getInstance();
		ilSession::set(self::SESSION_ID, NULL);
	}

    /**
     * @throws ilCtrlException
     */
    public function executeCommand(): void
    {
        $next = $this->ctrl->getNextClass();
        $cmd = $this->ctrl->getCmd(self::CMD_INDEX);
        switch ($next) {
            case strtolower(ilPropertyFormGUI::class):
                $usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
                switch ($_GET['exp_cont']) {
                    case 'il_expl2_jstree_cont_rep_exp_sel_repo':
                        //$usrdefUserTableGUI->getCrsSelectorGUI()->handleExplorerCommand();
                        break;
                    case 'il_expl2_jstree_cont_rep_exp_sel_orgu':
                        //todo
                        //$usrdefUserTableGUI->getOrguSelectorGUI()->handleExplorerCommand();
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

    protected function index(): void
    {
        $usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
        $this->ui->mainTemplate()->setContent($usrdefUserTableGUI->getHTML());
    }

    /**
     * @throws ilCtrlException
     */
    protected function applyFilter(): void
    {
        $usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
        $usrdefUserTableGUI->resetOffset();
        $usrdefUserTableGUI->writeFilterToSession();
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    /**
     * @throws ilCtrlException
     */
    protected function resetFilter(): void
    {
        $usrdefUserTableGUI = new usrdefUserTableGUI($this, self::CMD_INDEX);
        $usrdefUserTableGUI->resetFilter();
        $usrdefUserTableGUI->resetOffset();
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    protected function confirmSelectUser()
    {
        // Optinal
    }


    /**
     * @throws ilCtrlException
     * @throws \srag\DIC\UserDefaults\Exception\DICException
     */
    protected function selectUser(): void
    {
        $usr_ids = $_POST['id'];
        $user_objects = array();
        if ((is_array($usr_ids) && count($usr_ids) === 0) || !is_array($usr_ids)) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('failure', $this->pl->txt('msg_no_users_selected'), true);
            $this->ctrl->redirect($this, self::CMD_INDEX);
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
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('userdef_users_assigned', "", [count($usr_ids)]), true);
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }
}
