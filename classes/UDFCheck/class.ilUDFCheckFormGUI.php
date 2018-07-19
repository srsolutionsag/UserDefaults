<?php

/**
 * Class ilUDFCheckFormGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUDFCheckFormGUI extends ilPropertyFormGUI {

	const F_UDF_FIELD_ID = 'udf_field_id';
	const F_CHECK_VALUE = 'check_value';
	const F_CHECK_VALUE_MUL = 'check_value_mul_';
	const F_UDF_NEGATE_ID = 'udf_negate_value';
	const F_UDF_OPERATOR = 'udf_operator';
	const F_CHECK_RADIO = 'check_radio';
	const F_CHECK_TEXT = 'check_text';
	const F_CHECK_SELECT = 'check_select';

	const ILIAS_VERSION_5_2 = 2;

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
	 * @param ilUDFCheck    $ilUDFCheck
	 */
	public function __construct(ilUDFCheckGUI $parent_gui, ilUDFCheck $ilUDFCheck) {
		parent::__construct();

		global $DIC;

		$this->parent_gui = $parent_gui;
		$this->object = $ilUDFCheck;
		$this->is_new = $ilUDFCheck->getId() == 0;
		$this->ctrl = $DIC->ctrl();
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
			$te = new ilHiddenInputGUI($this->txt(self::F_UDF_FIELD_ID), self::F_UDF_FIELD_ID); // TODO Fix PostVar
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

					//Do not use ilCustomUserFieldsHelper for ILIAS 5.2 - bebause it's not available
					if($this->isCustomUserFieldsHelperAvailable()) {
						require_once "./Services/User/classes/class.ilCustomUserFieldsHelper.php";
						$plugin = ilCustomUserFieldsHelper::getInstance()->getPluginForType($udf_type);
						if ($plugin instanceof ilUDFDefinitionPlugin) {

							$select_gui = $plugin->getFormPropertyForDefinition($definition);

							$check_radio = new ilRadioGroupInputGUI("", self::F_CHECK_RADIO);

							$check_radio_text = new ilRadioOption($this->pl->txt("check_text_fields"), self::F_CHECK_TEXT);
							$check_radio->addOption($check_radio_text);

							foreach ($select_gui->getColumnDefinition() as $key => $name) {
								$text_gui = new ilTextInputGUI($name, self::F_CHECK_VALUE_MUL . $key);
								$check_radio_text->addSubItem($text_gui);
							}

							$check_radio_select = new ilRadioOption($this->pl->txt("check_select_lists"), self::F_CHECK_SELECT);
							$check_radio->addOption($check_radio_select);

							$select_gui->setPostVar(self::F_CHECK_VALUE);
							$select_gui->setRequired(false);
							$check_radio_select->addSubItem($select_gui);

							$this->addItem($check_radio);
						}
					}
					break;
			}
		}

		$this->addCommandButtons();
	}


	public function fillForm() {
		$array = array(
			self::F_UDF_FIELD_ID => $this->object->getUdfFieldId(),
			self::F_CHECK_VALUE => $this->object->getCheckValue(),
			self::F_UDF_NEGATE_ID => $this->object->isNegated(),
			self::F_UDF_OPERATOR => $this->object->getOperator(),
			self::F_CHECK_RADIO => self::F_CHECK_TEXT
		);

		$udf_type = ilUDFCheck::getDefinitionTypeForId($this->object->getUdfFieldId());
		$definition = ilUDFCheck::getDefinitionForId($udf_type);


		//DHBW Spec
		if($this->isCustomUserFieldsHelperAvailable()) {
				require_once "./Services/User/classes/class.ilCustomUserFieldsHelper.php";
				$plugin = ilCustomUserFieldsHelper::getInstance()->getPluginForType($udf_type);
				if ($plugin instanceof ilUDFDefinitionPlugin) {
					$select_gui = $plugin->getFormPropertyForDefinition($definition);

					$check_values = $this->object->getCheckValues();
					foreach ($select_gui->getColumnDefinition() as $key => $name) {
						$array[self::F_CHECK_VALUE_MUL . $key] = $check_values[$key];
					}
				}
		}

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
			$check_radio = $this->getInput(self::F_CHECK_RADIO);
			switch ($check_radio) {
				case self::F_CHECK_TEXT:
					$udf_type = ilUDFCheck::getDefinitionTypeForId($this->object->getUdfFieldId());
					$definition = ilUDFCheck::getDefinitionForId($udf_type);

					//DHBW Spec
					if($this->isCustomUserFieldsHelperAvailable()) {
						require_once "./Services/User/classes/class.ilCustomUserFieldsHelper.php";
						$plugin = ilCustomUserFieldsHelper::getInstance()->getPluginForType($udf_type);
						if ($plugin instanceof ilUDFDefinitionPlugin) {
							$select_gui = $plugin->getFormPropertyForDefinition($definition);
							$check_values = [];
							foreach ($select_gui->getColumnDefinition() as $key => $name) {
								$check_values[] = $this->getInput(self::F_CHECK_VALUE_MUL . $key);
							}
							$this->object->setCheckValues($check_values);
						}
					}
					break;
				case self::F_CHECK_SELECT:
				default:
					$this->object->setCheckValue($this->getInput(self::F_CHECK_VALUE));
					break;
			}
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


	/**
	 * @return bool
	 *
	 * CustomUserFieldsHelper is only available for ILIAS 5.3 and above!
	 */
	private function isCustomUserFieldsHelperAvailable() {
		return file_exists("./Services/User/classes/class.ilCustomUserFieldsHelper.php");
	}
}

