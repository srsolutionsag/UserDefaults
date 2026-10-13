<?php

use ILIAS\Refinery\Factory as Refinery;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;

/**
 * @ilCtrl_IsCalledBy usrdefUserGUI : ilUserDefaultsConfigGUI
 */
class usrdefUserGUI implements ilCtrlSecurityInterface
{
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    public const CMD_INDEX = 'index';
    public const CMD_CONFIRM_SELECT_USER = 'confirmSelectUser';
    public const CMD_SELECT_USER = 'selectUser';
    /**
     * Every selected user runs through all manual settings in one request, so
     * larger selections are refused instead of timing out half way through.
     */
    public const MAX_USERS = 500;
    private const POST_USR_ID = 'usr_id';

    private ilCtrl $ctrl;
    private ilUserDefaultsPlugin $pl;
    private ilGlobalTemplateInterface $main_tpl;
    private ILIAS\HTTP\Wrapper\WrapperFactory $wrapper;
    private Refinery $refinery;
    private ilLanguage $lng;

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
        $this->lng = $DIC->language();
    }

    /**
     * @throws ilCtrlException
     */
    public function executeCommand(): void
    {
        $cmd = $this->ctrl->getCmd(self::CMD_INDEX);
        match ($cmd) {
            self::CMD_INDEX,
            self::CMD_CONFIRM_SELECT_USER,
            self::CMD_SELECT_USER => $this->{$cmd}(),
            default => throw new ilException("Command not found: $cmd"),
        };
    }

    protected function index(): void
    {
        $this->main_tpl->setContent((new usrdefUserTableGUI($this))->render());
    }

    /**
     * Assigning changes data for every selected user, so it must not run off a
     * GET link the table action produces. ilCtrl checks the token of POST
     * commands of this class, and refuses selectUser as a GET command.
     */
    public function getUnsafeGetCommands(): array
    {
        return [self::CMD_SELECT_USER];
    }

    public function getSafePostCommands(): array
    {
        return [];
    }

    protected function confirmSelectUser(): void
    {
        $usr_ids = $this->getSelectedUserIds(new usrdefUserTableGUI($this));
        if (!$this->isSelectionAcceptable($usr_ids)) {
            $this->ctrl->redirect($this, self::CMD_INDEX);
            return;
        }

        $confirmation = new ilConfirmationGUI();
        $confirmation->setFormAction($this->ctrl->getFormAction($this));
        $confirmation->setHeaderText(sprintf($this->pl->txt('confirm_select_user'), count($usr_ids)));
        foreach ($usr_ids as $usr_id) {
            $name = ilObjUser::_lookupName($usr_id);
            $confirmation->addItem(
                self::POST_USR_ID . '[]',
                (string) $usr_id,
                trim($name['lastname'] . ', ' . $name['firstname'], ', ') . ' [' . $name['login'] . ']'
            );
        }
        $confirmation->setConfirm($this->pl->txt('button_select_user'), self::CMD_SELECT_USER);
        $confirmation->setCancel($this->lng->txt('cancel'), self::CMD_INDEX);

        $this->main_tpl->setContent($confirmation->getHTML());
    }

    protected function selectUser(): void
    {
        $usr_ids = $this->wrapper->post()->has(self::POST_USR_ID)
            ? array_values(array_filter($this->wrapper->post()->retrieve(
                self::POST_USR_ID,
                $this->refinery->kindlyTo()->listOf($this->refinery->kindlyTo()->int())
            )))
            : [];

        if (!$this->isSelectionAcceptable($usr_ids)) {
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
     * @param list<int> $usr_ids
     */
    private function isSelectionAcceptable(array $usr_ids): bool
    {
        if ($usr_ids === []) {
            $this->main_tpl->setOnScreenMessage('failure', $this->pl->txt('msg_no_users_selected'), true);
            return false;
        }

        if (count($usr_ids) > self::MAX_USERS) {
            $this->main_tpl->setOnScreenMessage(
                'failure',
                sprintf($this->pl->txt('msg_too_many_users_selected'), self::MAX_USERS),
                true
            );
            return false;
        }

        return true;
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
            return $table->getAllMatchingUserIds(self::MAX_USERS + 1);
        }

        return array_values(array_filter(array_map('intval', $ids)));
    }
}
