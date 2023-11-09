<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\UserDefaults\DICTrait;
use srag\DIC\UserDefaults\Exception\DICException;
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

	use DICTrait;
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


	/**
	 * UDFCheckGUI constructor
	 *
	 * @param UserSettingsGUI|UDFCheckGUI $parent_gui
	 */
	public function __construct($parent_gui) {
		self::dic()->ctrl()->saveParameter($this, self::IDENTIFIER_CATEGORY);
		self::dic()->ctrl()->saveParameter($this, self::IDENTIFIER);
		self::dic()->ctrl()->setParameter($this, UserSettingsGUI::IDENTIFIER, $_GET[UserSettingsGUI::IDENTIFIER]);
		self::dic()->ctrl()->saveParameter($parent_gui, UserSettingsGUI::IDENTIFIER);
	}



	public function executeCommand(): bool
    {
		$cmd = self::dic()->ctrl()->getCmd(self::CMD_INDEX);
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
		self::output()->output($ilUDFCheckTabeGUI);
	}


	protected function add(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this);
		$ilUDFCheckFormGUI->fillForm();
		self::output()->output($ilUDFCheckFormGUI);
	}


	protected function create(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this);
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($id = $ilUDFCheckFormGUI->saveObject()) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('success', self::plugin()->translate('msg_entry_added'), true);
			self::dic()->ctrl()->setParameter($this, self::IDENTIFIER_CATEGORY, $ilUDFCheckFormGUI->getObject()->getFieldCategory());
			self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, $id);
			self::dic()->ctrl()->redirect($this, self::CMD_EDIT);
		}
		self::output()->output($ilUDFCheckFormGUI);
	}


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    protected function edit(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this, $this->getObject());
		$ilUDFCheckFormGUI->fillForm();
		self::output()->output($ilUDFCheckFormGUI);
	}


	protected function update(): void
    {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this, $this->getObject());
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($ilUDFCheckFormGUI->saveObject()) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('success',self::plugin()->translate('msg_entry_added'), true);
			$this->cancel();
		}
		self::output()->output($ilUDFCheckFormGUI);
	}

	public function confirmDelete(): void
    {
		$conf = new ilConfirmationGUI();
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setHeaderText(self::plugin()->translate('msg_confirm_delete'));
		$conf->setConfirm(self::plugin()->translate('check_delete'), self::CMD_DELETE);
		$conf->setCancel(self::plugin()->translate('check_cancel'), self::CMD_INDEX);
		self::output()->output($conf);
	}


	public function delete(): void
    {
		$ilUDFCheck = $this->getObject();
		$ilUDFCheck->delete();
		$this->cancel();
	}


	public function cancel(): void
    {
		self::dic()->ctrl()->setParameter($this, self::IDENTIFIER_CATEGORY, NULL);
		self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, NULL);
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	protected function getObject(): ?UDFCheck
    {
		return UDFCheck::getCheckById((int) filter_input(INPUT_GET, UDFCheckGUI::IDENTIFIER_CATEGORY), (int) filter_input(INPUT_GET, UDFCheckGUI::IDENTIFIER));
	}
}
