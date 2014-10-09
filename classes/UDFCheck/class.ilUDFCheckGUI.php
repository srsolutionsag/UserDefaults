<?php
require_once('class.ilUDFCheckTableGUI.php');
require_once('class.ilUDFCheckFormGUI.php');

/**
 * Class ilUDFCheckGUI
 *
 * @author            Fabian Schmid <fs@studer-raimann.ch>
 * @version           1.0.0
 *
 * @ilCtrl_IsCalledBy ilUDFCheckGUI: ilUserDefaultsConfigGUI
 */
class ilUDFCheckGUI {

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
	const IDENTIFIER = 'check_id';
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var HTML_Template_ITX|ilTemplate
	 */
	protected $tpl;


	/**
	 * @param $parent_gui
	 */
	public function __construct($parent_gui) {
		global $ilCtrl, $tpl;
		/**
		 * @var $ilCtrl ilCtrl
		 */
		$this->ctrl = $ilCtrl;
		$this->tpl = $tpl;
		$this->pl = ilUserDefaultsPlugin::getInstance();
		$this->ctrl->saveParameter($this, self::IDENTIFIER);
		$this->ctrl->setParameter($this, ilUserSettingsGUI::IDENTIFIER, $_GET[ilUserSettingsGUI::IDENTIFIER]);
		$this->ctrl->saveParameter($parent_gui, ilUserSettingsGUI::IDENTIFIER);
	}


	public function executeCommand() {
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


	protected function index() {
		$ilUDFCheckTabeGUI = new ilUDFCheckTableGUI($this);
		$this->tpl->setContent($ilUDFCheckTabeGUI->getHTML());
	}


	protected function add() {
		$ilUDFCheckFormGUI = new ilUDFCheckFormGUI($this, new ilUDFCheck());
		$this->tpl->setContent($ilUDFCheckFormGUI->getHTML());
	}


	protected function create() {
		$ilUDFCheckFormGUI = new ilUDFCheckFormGUI($this, new ilUDFCheck());
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($id = $ilUDFCheckFormGUI->saveObject()) {
			ilUtil::sendSuccess($this->pl->txt('msg_entry_added'), true);
			$this->ctrl->setParameter($this, self::IDENTIFIER, $id);
			$this->ctrl->redirect($this, self::CMD_EDIT);
		}
		$this->tpl->setContent($ilUDFCheckFormGUI->getHTML());
	}


	protected function edit() {
		$ilUserSettingsFormGUI = new ilUDFCheckFormGUI($this, ilUDFCheck::find($_GET[self::IDENTIFIER]));
		$ilUserSettingsFormGUI->fillForm();
		$this->tpl->setContent($ilUserSettingsFormGUI->getHTML());
	}


	protected function update() {
		$ilUDFCheckFormGUI = new ilUDFCheckFormGUI($this, ilUDFCheck::find($_GET[self::IDENTIFIER]));
		$ilUDFCheckFormGUI->setValuesByPost();
		if ($ilUDFCheckFormGUI->saveObject()) {
			ilUtil::sendSuccess($this->pl->txt('msg_entry_added'), true);
			$this->cancel();
		}
		$this->tpl->setContent($ilUDFCheckFormGUI->getHTML());
	}


	public function confirmDelete() {
		$conf = new ilConfirmationGUI();
		$conf->setFormAction($this->ctrl->getFormAction($this));
		$conf->setHeaderText($this->pl->txt('msg_confirm_delete'));
		$conf->setConfirm($this->pl->txt('check_delete'), self::CMD_DELETE);
		$conf->setCancel($this->pl->txt('check_cancel'), self::CMD_INDEX);
		$this->tpl->setContent($conf->getHTML());
	}


	public function delete() {
		$ilUDFCheck = ilUDFCheck::find($_GET[self::IDENTIFIER]);
		$ilUDFCheck->delete();
		$this->cancel();
	}


	public function cancel() {
		$this->ctrl->setParameter($this, self::IDENTIFIER, NULL);
		$this->ctrl->redirect($this, self::CMD_INDEX);
	}



	//
	//	protected function activate() {
	//		$ilUserSetting = ilUserSetting::find($_GET[self::IDENTIFIER]);
	//		$ilUserSetting->setStatus(ilUserSetting::STATUS_ACTIVE);
	//		$ilUserSetting->update();
	//		$this->cancel();
	//	}
	//
	//
	//	protected function deactivate() {
	//		$ilUserSetting = ilUserSetting::find($_GET[self::IDENTIFIER]);
	//		$ilUserSetting->setStatus(ilUserSetting::STATUS_INACTIVE);
	//		$ilUserSetting->update();
	//		$this->cancel();
	//	}
}

?>
