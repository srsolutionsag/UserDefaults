<?php

use ILIAS\HTTP\Services;
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
    private ilGlobalTemplateInterface $main_tpl;
    private UIServices $ui;
    private Services $http;

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
        $this->main_tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilUserDefaultsPlugin::getInstance();
        ilSession::set(self::SESSION_ID, null);
        $this->http = $DIC->http();
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand(): void
    {
        $cmd = $this->ctrl->getCmd(self::CMD_INDEX);
        match ($cmd) {
            self::CMD_INDEX,
            self::CMD_APPLY_FILTER,
            self::CMD_RESET_FILTER,
            self::CMD_SELECT_USER => $this->{$cmd}(),
            default => throw new ilException("Command not found: $cmd"),
        };
    }

    protected function index(): void
    {
        $table = new usrdefUserTableGUI($this, self::CMD_INDEX);
        $this->main_tpl->setContent($table->getHTML());
    }

    protected function applyFilter(): void
    {
        $table = new usrdefUserTableGUI($this, self::CMD_INDEX);
        $table->resetOffset();
        $table->writeFilterToSession();
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    protected function resetFilter(): void
    {
        $tbale = new usrdefUserTableGUI($this, self::CMD_INDEX);
        $tbale->resetFilter();
        $tbale->resetOffset();
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }

    protected function selectUser(): void
    {
        $usr_ids = $this->http->request()->getParsedBody()['id'] ?? [];

        if ($usr_ids === []) {
            $this->main_tpl->setOnScreenMessage(
                'failure',
                $this->pl->txt('msg_no_users_selected'),
                true
            );
            $this->ctrl->redirect($this, self::CMD_INDEX);
            return;
        }

        // Create ilObjUser instances
        $user_objects = [];
        foreach ($usr_ids as $usr_id) {
            try {
                $user_objects[] = new ilObjUser($usr_id);
            } catch (Throwable) {
                continue;
            }
        }

        // Apply user settings
        $user_settings = UserSetting::where(['status' => UserSetting::STATUS_ACTIVE, 'on_manual' => true])->get();
        foreach ($user_settings as $user_setting) {
            /** @var UserSetting $user_setting */
            $user_setting->doMultipleAssignements($user_objects);
        }

        // Confirm and redirect
        $this->main_tpl->setOnScreenMessage(
            'success',
            sprintf($this->pl->txt('userdef_users_assigned'), count($user_objects)),
            true
        );
        $this->ctrl->redirect($this, self::CMD_INDEX);
    }
}
