<?php

use ILIAS\Refinery\Factory as Refinery;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;

/**
 * @ilCtrl_IsCalledBy usrdefUserGUI : ilUserDefaultsConfigGUI
 */
class usrdefUserGUI
{
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    public const CMD_INDEX = 'index';
    public const CMD_SELECT_USER = 'selectUser';

    private ilCtrl $ctrl;
    private ilUserDefaultsPlugin $pl;
    private ilGlobalTemplateInterface $main_tpl;
    private ILIAS\HTTP\Wrapper\WrapperFactory $wrapper;
    private Refinery $refinery;

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
        $this->main_tpl = $DIC->ui()->mainTemplate();
        $this->pl = ilUserDefaultsPlugin::getInstance();
        $this->wrapper = $DIC->http()->wrapper();
        $this->refinery = $DIC->refinery();
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand(): void
    {
        $cmd = $this->ctrl->getCmd(self::CMD_INDEX);
        match ($cmd) {
            self::CMD_INDEX,
            self::CMD_SELECT_USER => $this->{$cmd}(),
            default => throw new ilException("Command not found: $cmd"),
        };
    }

    protected function index(): void
    {
        $this->main_tpl->setContent((new usrdefUserTableGUI($this))->render());
    }

    protected function selectUser(): void
    {
        $table = new usrdefUserTableGUI($this);
        $usr_ids = $this->getSelectedUserIds($table);

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

    /**
     * The table hands over the selected row ids, or a single ALL_OBJECTS marker
     * when everything the current filter matches was selected.
     *
     * @return list<int>
     */
    private function getSelectedUserIds(usrdefUserTableGUI $table): array
    {
        $parameter = implode('_', usrdefUserTableGUI::NAMESPACE) . '_' . usrdefUserTableGUI::TOKEN_ROW_ID;

        if (!$this->wrapper->query()->has($parameter)) {
            return [];
        }

        $ids = $this->wrapper->query()->retrieve(
            $parameter,
            $this->refinery->kindlyTo()->listOf($this->refinery->kindlyTo()->string())
        );

        if (in_array(usrdefUserTableGUI::ALL_OBJECTS, $ids, true)) {
            return $table->getAllMatchingUserIds();
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }
}
