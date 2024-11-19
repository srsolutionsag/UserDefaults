<?php

use ILIAS\DI\UIServices;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * @ilCtrl_IsCalledBy usrdefUserGUI : ilUserDefaultsConfigGUI
 * @ilCtrl_Calls      usrdefUserGUI : ilpropertyformgui
 */
class usrdefUserGUI
{
    use UserDefaultsTrait;

    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    public const CMD_INDEX = 'index';
    public const CMD_APPLY_FILTER = 'applyFilter';
    public const CMD_RESET_FILTER = 'resetFilter';
    public const CMD_SELECT_USER = 'selectUser';
    public const CMD_CONFIRM = 'confirmSelectUser';
    public const IDENTIFIER = 'usr_id';
    public const SESSION_ID = 'multi_assign_user_id';
    private ilCtrl $ctrl;
    private ilUserDefaultsPlugin $pl;
    private ilGlobalTemplateInterface $tpl;
    private UIServices $ui;

    /**
     * usrdefUserGUI constructor
     */
    public function __construct()
    {
        global $DIC;
        //Check Access
        if (!ilUserDefaultsPlugin::grantAccess()) {
            echo "no Search Permission";
            exit;
        };

        $this->ctrl = $DIC->ctrl();
        $this->ui = $DIC->ui();
        $this->tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilUserDefaultsPlugin::getInstance();
        ilSession::set(self::SESSION_ID, null);
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
        $user_objects = [];
        if ((is_array($usr_ids) && $usr_ids === []) || !is_array($usr_ids)) {
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
        foreach (
            UserSetting::where(['status' => UserSetting::STATUS_ACTIVE, 'on_manual' => true])->get() as $ilUserSetting
        ) {
            $ilUserSetting->doMultipleAssignements($user_objects);
        }
        $this->tpl->setOnScreenMessage('success', $this->pl->txt('userdef_users_assigned'), true);
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }
}
