<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\UserDefaults\UDFCheck\UDFCheck;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheckFormGUI;
use srag\Plugins\UserDefaults\UDFCheck\UDFCheckTableGUI;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class UDFCheckGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy UDFCheckGUI: ilUserDefaultsConfigGUI
 */
class UDFCheckGUI {

	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const CMD_INDEX = 'index';
	const CMD_CANCEL = 'cancel';
	const CMD_CREATE = 'create';
	const CMD_UPDATE = 'update';
	const CMD_ADD = 'add';
	const CMD_EDIT = 'edit';
	const CMD_CONFIRM_DELETE = 'confirmDelete';
	const CMD_DEACTIVATE = 'deactivate';
	const CMD_ACTIVATE = 'activate';
	const CMD_DELETE = 'delete';
	const IDENTIFIER_CATEGORY = 'field_category';
	const IDENTIFIER = 'check_id';
    private ilCtrl $ctrl;
    private ilUserDefaultsPlugin $pl;
    private \ILIAS\DI\UIServices $ui;

    /**
     * @throws ilCtrlException
     */
	public function __construct(UserSettingsGUI|UDFCheckGUI $parent_gui) {
        global $DIC;
        //check Access
        if(!ilUserDefaultsPlugin::grantAccess()) {
            echo "no UDFCheck Permission";
            exit;
        };

        $this->ctrl = $DIC->ctrl();
        $this->ui = $DIC->ui();
        $this->pl = ilUserDefaultsPlugin::getInstance();
        $this->ctrl->saveParameter($this, self::IDENTIFIER_CATEGORY);
        $this->ctrl->saveParameter($this, self::IDENTIFIER);
        $this->ctrl->setParameter($this, UserSettingsGUI::IDENTIFIER, $_GET[UserSettingsGUI::IDENTIFIER]);
        $this->ctrl->saveParameter($parent_gui, UserSettingsGUI::IDENTIFIER);
	}

	public function executeCommand(): bool
    {
		$cmd = $this->ctrl->getCmd(self::CMD_INDEX);
		switch ($cmd) {
			case self::CMD_INDEX:
			case self::CMD_CANCEL:
			case self::CMD_CREATE:
			case self::CMD_UPDATE:
			case self::CMD_ADD:
			case self::CMD_EDIT:
			case self::CMD_ACTIVATE:
			case self::CMD_DEACTIVATE:
			case self::CMD_CONFIRM_DELETE:
			case self::CMD_DELETE:
				$this->{$cmd}();
				break;
		}
		return true;
	}

	protected function index(): void
    {
		$ilUDFCheckTabeGUI = new UDFCheckTableGUI($this);
        $this->ui->mainTemplate()->setContent($ilUDFCheckTabeGUI->getHTML());
	}

	protected function add(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this);
		$ilUDFCheckFormGUI->fillForm();
        $this->ui->mainTemplate()->setContent($ilUDFCheckFormGUI->getHTML());
	}

	protected function create(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this);
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($id = $ilUDFCheckFormGUI->saveObject()) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('success', $this->pl->txt('msg_entry_added'), true);
			$this->ctrl->setParameter($this, self::IDENTIFIER_CATEGORY, $ilUDFCheckFormGUI->getObject()->getFieldCategory());
            $this->ctrl->setParameter($this, self::IDENTIFIER, $id);
            $this->ctrl->redirect($this, self::CMD_EDIT);
		}

        $this->ui->mainTemplate()->setContent($ilUDFCheckFormGUI->getHTML());
	}

    protected function edit(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this, $this->getObject());
		$ilUDFCheckFormGUI->fillForm();
        $this->ui->mainTemplate()->setContent($ilUDFCheckFormGUI->getHTML());
	}

	protected function update(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this, $this->getObject());
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($ilUDFCheckFormGUI->saveObject()) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('success',$this->pl->txt('msg_entry_added'), true);
			$this->cancel();
		}
        $this->ui->mainTemplate()->setContent($ilUDFCheckFormGUI->getHTML());
	}

    /**
     * @throws ilCtrlException
     */
    public function confirmDelete(): void
    {
		$conf = new ilConfirmationGUI();
		$conf->setFormAction($this->ctrl->getFormAction($this));

        $conf->setHeaderText($this->pl->txt('msg_confirm_delete'));
		$conf->setConfirm($this->pl->txt('check_delete'), self::CMD_DELETE);
		$conf->setCancel($this->pl->txt('check_cancel'), self::CMD_INDEX);

        $this->ui->mainTemplate()->setContent($conf->getHTML());
	}


    /**
     * @throws ilCtrlException
     */
    public function delete(): void
    {
		$ilUDFCheck = $this->getObject();
		$ilUDFCheck->delete();
		$this->cancel();
	}


    /**
     * @throws ilCtrlException
     */
    public function cancel(): void
    {
        $this->ctrl->setParameter($this, self::IDENTIFIER_CATEGORY, NULL);
        $this->ctrl->setParameter($this, self::IDENTIFIER, NULL);
        $this->ctrl->redirect($this, self::CMD_INDEX);
	}

	protected function getObject(): ?UDFCheck
    {
		return UDFCheck::getCheckById((int) filter_input(INPUT_GET, UDFCheckGUI::IDENTIFIER_CATEGORY), (int) filter_input(INPUT_GET, UDFCheckGUI::IDENTIFIER));
	}
}
