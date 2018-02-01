<?php
require_once('./Services/Form/classes/class.ilPropertyFormGUI.php');

/**
 * Class ilUDFCheckFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUDFCheckFormGUI extends ilPropertyFormGUI {

	const F_UDF_FIELD_ID = 'udf_field_id';
	const F_CHECK_VALUE = 'check_value';
	const F_UDF_NEGATE_ID = 'udf_negate_value';
	const F_UDF_OPERATOR = 'udf_operator';
	/**
	 * @var ilUserSettingsGUI
	 */
	protected $parent_gui;
	/**
	 * @var ilUDFCheck
	 */
	protected $object;
	/**
	 * @var bool
	 */
	protected $is_new = true;
	/**
	 * @var ilCtrl
	 */
	protected $ctrl;
	/**
	 * @var \ilUserDefaultsPlugin
	 */
	protected $pl;


	/**
	 * @param ilUDFCheckGUI $parent_gui
	 * @param ilUDFCheck $ilUDFCheck
	 */
	public function __construct(ilUDFCheckGUI $parent_gui, ilUDFCheck $ilUDFCheck) {
		global $ilCtrl;

		$this->parent_gui = $parent_gui;
		$this->object = $ilUDFCheck;
		$this->is_new = $ilUDFCheck->getId() == 0;
		$this->ctrl = $ilCtrl;
		$this->pl = ilUserDefaultsPlugin::getInstance();
		$this->setFormAction($this->ctrl->getFormAction($this->parent_gui));
		$this->initForm();
	}


	/**
	 * @param $key
	 *
	 * @return string
	 */
	protected function txt($key) {
		return $this->pl->txt('check_' . $key);
	}


	protected function initForm() {
		$this->setTitle($this->pl->txt('form_title'));
		$te = new ilSelectInputGUI($this->txt(self::F_UDF_FIELD_ID), self::F_UDF_FIELD_ID);
		$te->setDisabled(!$this->is_new);
		$te->setRequired(true);
		$te->setOptions(ilUDFCheck::getDefinitionData());
		$this->addItem($te);

		if (!$this->is_new) {
			$te = new ilHiddenInputGUI($this->txt(self::F_UDF_FIELD_ID), self::F_UDF_FIELD_ID);
			$this->addItem($te);

			$cb = new ilCheckboxInputGUI($this->txt(self::F_UDF_NEGATE_ID), self::F_UDF_NEGATE_ID);
			$cb->setInfo($this->txt(self::F_UDF_NEGATE_ID . "_info"));
			$this->addItem($cb);

			$op = new ilSelectInputGUI($this->txt(self::F_UDF_OPERATOR), self::F_UDF_OPERATOR);
			$op->setInfo($this->pl->txt('check_op_reg_ex_info'));
			$options = array();
			foreach (ilUDFCheck::$operator_text_keys as $key => $v) {
				$options[$key] = $this->pl->txt("check_op_" . $v);
			}
			$op->setOptions($options);
			$this->addItem($op);

			$udf_type = ilUDFCheck::getDefinitionTypeForId($this->object->getUdfFieldId());
			$definition = ilUDFCheck::getDefinitionForId($udf_type);
			switch ($udf_type) {
				case ilUDFCheck::TYPE_TEXT:
					$se = new ilTextInputGUI($this->pl->txt(self::F_CHECK_VALUE), self::F_CHECK_VALUE);
					$this->addItem($se);
					break;
				case ilUDFCheck::TYPE_SELECT:
					$se = new ilSelectInputGUI($this->pl->txt(self::F_CHECK_VALUE), self::F_CHECK_VALUE);
					$se->setOptions(ilUDFCheck::getDefinitionValuesForId($this->object->getUdfFieldId()));
					$this->addItem($se);
					break;
				default:
					require_once('./Services/User/classes/class.ilCustomUserFieldsHelper.php');
					$plugin = ilCustomUserFieldsHelper::getInstance()->getPluginForType($udf_type);
					if ($plugin instanceof ilUDFDefinitionPlugin) {
						$input_gui = $plugin->getFormPropertyForDefinition($definition);
						$input_gui->setPostVar(self::F_CHECK_VALUE);
						$input_gui->setRequired(false);
						$this->addItem($input_gui);
					}

					break;
			}
		}

		$this->addCommandButtons();
	}


	public function fillForm() {
		$array = array(
			self::F_UDF_FIELD_ID  => $this->object->getUdfFieldId(),
			self::F_CHECK_VALUE   => $this->object->getCheckValue(),
			self::F_UDF_NEGATE_ID => $this->object->isNegated(),
			self::F_UDF_OPERATOR  => $this->object->getOperator(),
		);

		$this->setValuesByArray($array);
	}


	/**
	 * @return bool
	 */
	public function saveObject() {
		if (!$this->checkInput()) {
			return false;
		}

		if (!$this->is_new) {
			$this->object->setCheckValue($this->getInput(self::F_CHECK_VALUE));
			$this->object->setNegated($this->getInput(self::F_UDF_NEGATE_ID));
			$this->object->setOperator($this->getInput(self::F_UDF_OPERATOR));
			$this->object->update();
		} else {
			$this->object->setUdfFieldId($this->getInput(self::F_UDF_FIELD_ID));
			$this->object->setParentId($_GET[ilUserSettingsGUI::IDENTIFIER]);
			$this->object->create();
		}

		return $this->object->getId();
	}


	protected function addCommandButtons() {
		if (!$this->is_new) {
			$this->addCommandButton(ilUDFCheckGUI::CMD_UPDATE, $this->pl->txt('form_button_update'));
		} else {
			$this->addCommandButton(ilUDFCheckGUI::CMD_CREATE, $this->pl->txt('form_button_create'));
		}
		$this->addCommandButton(ilUDFCheckGUI::CMD_CANCEL, $this->pl->txt('form_button_cancel'));
	}
}

