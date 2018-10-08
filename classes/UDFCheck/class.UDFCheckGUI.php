<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\DIC\DICTrait;
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
	 * @param $parent_gui
	 */
	public function __construct($parent_gui) {
		self::dic()->ctrl()->saveParameter($this, self::IDENTIFIER_CATEGORY);
		self::dic()->ctrl()->saveParameter($this, self::IDENTIFIER);
		self::dic()->ctrl()->setParameter($this, UserSettingsGUI::IDENTIFIER, $_GET[UserSettingsGUI::IDENTIFIER]);
		self::dic()->ctrl()->saveParameter($parent_gui, UserSettingsGUI::IDENTIFIER);
	}


	/**
	 *
	 */
	public function executeCommand() {
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


	/**
	 *
	 */
	protected function index() {
		$ilUDFCheckTabeGUI = new UDFCheckTableGUI($this);
		self::dic()->template()->setContent($ilUDFCheckTabeGUI->getHTML());
	}


	/**
	 *
	 */
	protected function add() {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this);
		$ilUDFCheckFormGUI->fillForm();
		self::dic()->template()->setContent($ilUDFCheckFormGUI->getHTML());
	}


	/**
	 *
	 */
	protected function create() {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this);
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($id = $ilUDFCheckFormGUI->saveObject()) {
			ilUtil::sendSuccess(self::plugin()->translate('msg_entry_added'), true);
			self::dic()->ctrl()->setParameter($this, self::IDENTIFIER_CATEGORY, $ilUDFCheckFormGUI->getObject()->getFieldCategory());
			self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, $id);
			self::dic()->ctrl()->redirect($this, self::CMD_EDIT);
		}
		self::dic()->template()->setContent($ilUDFCheckFormGUI->getHTML());
	}


	/**
	 *
	 */
	protected function edit() {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this, $this->getObject());
		$ilUDFCheckFormGUI->fillForm();
		self::dic()->template()->setContent($ilUDFCheckFormGUI->getHTML());
	}


	/**
	 *
	 */
	protected function update() {
		$ilUDFCheckFormGUI = new UDFCheckFormGUI($this, $this->getObject());
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($ilUDFCheckFormGUI->saveObject()) {
			ilUtil::sendSuccess(self::plugin()->translate('msg_entry_added'), true);
			$this->cancel();
		}
		self::dic()->template()->setContent($ilUDFCheckFormGUI->getHTML());
	}


	/**
	 *
	 */
	public function confirmDelete() {
		$conf = new ilConfirmationGUI();
		$conf->setFormAction(self::dic()->ctrl()->getFormAction($this));
		$conf->setHeaderText(self::plugin()->translate('msg_confirm_delete'));
		$conf->setConfirm(self::plugin()->translate('check_delete'), self::CMD_DELETE);
		$conf->setCancel(self::plugin()->translate('check_cancel'), self::CMD_INDEX);
		self::dic()->template()->setContent($conf->getHTML());
	}


	/**
	 *
	 */
	public function delete() {
		$ilUDFCheck = $this->getObject();
		$ilUDFCheck->delete();
		$this->cancel();
	}


	/**
	 *
	 */
	public function cancel() {
		self::dic()->ctrl()->setParameter($this, self::IDENTIFIER_CATEGORY, NULL);
		self::dic()->ctrl()->setParameter($this, self::IDENTIFIER, NULL);
		self::dic()->ctrl()->redirect($this, self::CMD_INDEX);
	}


	/**
	 * @return UDFCheck|null
	 */
	protected function getObject() {
		return UDFCheck::getCheckById(filter_input(INPUT_GET, UDFCheckGUI::IDENTIFIER_CATEGORY), filter_input(INPUT_GET, UDFCheckGUI::IDENTIFIER));
	}
}
