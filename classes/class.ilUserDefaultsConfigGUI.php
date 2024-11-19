<?php

/**
 * @ilCtrl_isCalledBy ilUserDefaultsConfigGUI: ilObjComponentSettingsGUI
 */
class ilUserDefaultsConfigGUI extends ilPluginConfigGUI
{
    public const TAB_SETTINGS = "settings";
    public const TAB_USERS = "users";
    public const TAB_GLOBAL_SETTINGS = "global_settings";
    /**
     * @readonly
     */
    private ilCtrl $ctrl;
    /**
     * @readonly
     */
    private ilTabsGUI $tabs;
    /**
     * @readonly
     */
    private ilUserDefaultsPlugin $pl;

    /**
     * ilUserDefaultsConfigGUI constructor
     */
    public function __construct()
    {
        global $DIC;
        //Access granted?
        if (!ilUserDefaultsPlugin::grantAccess()) {
            echo "no Plugin Permission";
            exit;
        };

        $this->ctrl = $DIC->ctrl();
        $this->tabs = $DIC["ilTabs"];
        $this->pl = ilUserDefaultsPlugin::getInstance();
    }

    /**
     * @throws ilCtrlException
     */
    public function performCommand(string $cmd): void
    {
        $this->tabs->addTab(
            self::TAB_SETTINGS,
            $this->pl->txt('tabs_settings'),
            $this->ctrl
                ->getLinkTargetByClass(UserSettingsGUI::class)
        );
        $this->tabs->addTab(
            self::TAB_USERS,
            $this->pl->txt('tabs_users'),
            $this->ctrl
                ->getLinkTargetByClass(usrdefUserGUI::class)
        );

        switch ($this->ctrl->getNextClass()) {
            case strtolower(UDFCheckGUI::class):
                $this->tabs->activateTab(self::TAB_SETTINGS);
                $gui = new UDFCheckGUI(new UserSettingsGUI());
                break;
            case strtolower(usrdefUserGUI::class):
                $this->tabs->activateTab(self::TAB_USERS);
                $gui = new usrdefUserGUI();
                break;
            case strtolower(ilUserDefaultsRestApiGUI::class):
                $gui = new ilUserDefaultsRestApiGUI();
                break;
            default:
                $this->tabs->activateTab(self::TAB_SETTINGS);
                $gui = new UserSettingsGUI();
                break;
        }
        $this->ctrl->forwardCommand($gui);
    }
}
