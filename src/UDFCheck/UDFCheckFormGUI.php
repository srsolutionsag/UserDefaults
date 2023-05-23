<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ilCheckboxInputGUI;
use ilCustomUserFieldsHelper;
use ilPropertyFormGUI;
use ilRadioGroupInputGUI;
use ilRadioOption;
use ilSelectInputGUI;
use ilTextInputGUI;
use ilUDFDefinitionPlugin;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UDFCheckGUI;
use UserSettingsGUI;

/**
 * Class UDFCheckFormGUI
 *
 * @package srag\Plugins\UserDefaults\UDFCheck
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class UDFCheckFormGUI extends ilPropertyFormGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const F_UDF_FIELD_KEY = 'field_key';
	const F_UDF_FIELD_CATEGORY = 'field_category';
	const F_CHECK_VALUE = 'check_value';
	const F_CHECK_VALUE_MUL = 'check_value_mul_';
	const F_UDF_NEGATE_ID = 'udf_negate_value';
	const F_UDF_OPERATOR = 'udf_operator';
	const F_CHECK_RADIO = 'check_radio';
	const F_CHECK_TEXT = 'check_text';
	const F_CHECK_SELECT = 'check_select';
	/**
	 * @var UserSettingsGUI
	 */
	protected $parent_gui;
	/**
	 * @var UDFCheck
	 */
	protected $object;
	/**
	 * @var bool
	 */
	protected $is_new = true;


	/**
	 * @param UDFCheckGUI   $parent_gui
	 * @param UDFCheck|null $object
	 */
	public function __construct(UDFCheckGUI $parent_gui, UDFCheck $object = NULL) {
		parent::__construct();

		$this->parent_gui = $parent_gui;

		$this->object = $object;

		$this->is_new = ($this->object === NULL);

		$this->setFormAction(self::dic()->ctrl()->getFormAction($this->parent_gui));

		$this->initForm();
	}


	/**
	 * @param string $key
	 *
	 * @return string
	 */
	protected function txt($key) {
		return self::plugin()->translate($key, 'check');
	}


	/**
	 *
	 */
	protected function initForm() {
		$this->setTitle(self::plugin()->translate('form_title'));

		$categories_radio = new ilRadioGroupInputGUI($this->txt(self::F_UDF_FIELD_CATEGORY), self::F_UDF_FIELD_CATEGORY);
		$this->addItem($categories_radio);

		foreach (UDFCheck::$class_names as $key => $class) {
			$category_radio = new ilRadioOption($this->txt(self::F_UDF_FIELD_CATEGORY . "_" . $key), $key);
			$category_radio->setDisabled(!$this->is_new);

			$te = new ilSelectInputGUI($this->txt(self::F_UDF_FIELD_KEY), self::F_UDF_FIELD_KEY . "_" . $key);
			$te->setDisabled(!$this->is_new);
			$te->setRequired(true);
			$te->setOptions($class::getDefinitionsOfCategoryOptions());

			$category_radio->addSubItem($te);

			$categories_radio->addOption($category_radio);
		}

		if (!$this->is_new) {
			$cb = new ilCheckboxInputGUI($this->txt(self::F_UDF_NEGATE_ID), self::F_UDF_NEGATE_ID);
			$cb->setInfo($this->txt(self::F_UDF_NEGATE_ID . "_info"));
			$this->addItem($cb);

			$op = new ilSelectInputGUI($this->txt(self::F_UDF_OPERATOR), self::F_UDF_OPERATOR);
			$op->setInfo(self::plugin()->translate('check_op_reg_ex_info'));
			$options = array();
			foreach (UDFCheck::$operator_text_keys as $key => $v) {
				$options[$key] = self::plugin()->translate("check_op_" . $v);
			}
			$op->setOptions($options);
			$this->addItem($op);

			$definition = $this->object->getDefinition();

			switch ($definition["field_type"]) {
				case FIELD_TYPE_TEXT:
				case UDF_TYPE_TEXT:
					$se = new ilTextInputGUI(self::plugin()->translate(self::F_CHECK_VALUE), self::F_CHECK_VALUE);
					$this->addItem($se);
					break;
				case UDF_TYPE_SELECT:
				case FIELD_TYPE_SELECT:
				case FIELD_TYPE_MULTI:
					switch ($this->object->getFieldKey()) {
						case 'org_units':
							$se = new ilTextInputGUI(self::plugin()->translate(self::F_CHECK_VALUE), self::F_CHECK_VALUE);
							$this->addItem($se);
							break;
						default:
							$se = new ilSelectInputGUI(self::plugin()->translate(self::F_CHECK_VALUE), self::F_CHECK_VALUE);
							$se->setOptions($this->object->getDefinitionValues());
							$this->addItem($se);
							break;
					}
					break;
				default:
					//DHBW Spec
					if (self::isCustomUserFieldsHelperAvailable()) {
						$plugin = ilCustomUserFieldsHelper::getInstance()->getPluginForType($definition["field_type"]);
						if ($plugin instanceof ilUDFDefinitionPlugin) {



							$select_gui = $plugin->getFormPropertyForDefinition($definition,true,null,true);

							$check_radio = new ilRadioGroupInputGUI("", self::F_CHECK_RADIO);

							$check_radio_text = new ilRadioOption(self::plugin()->translate("check_text_fields"), self::F_CHECK_TEXT);
							$check_radio->addOption($check_radio_text);

							foreach ($select_gui->getColumnDefinition() as $key => $name) {
								if (is_array($name)) {
									$name=$name["name"]." ( ".$name["default"]." ) ";
								}

								$text_gui = new ilTextInputGUI($name, self::F_CHECK_VALUE_MUL . $key);
								$check_radio_text->addSubItem($text_gui);
							}

							$check_radio_select = new ilRadioOption(self::plugin()->translate("check_select_lists"), self::F_CHECK_SELECT);
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


	/**
	 *
	 */
	public function fillForm() {
		if (!$this->is_new) {
			$array = [
				self::F_UDF_FIELD_KEY . "_" . $this->object->getFieldCategory() => $this->object->getFieldKey(),
				self::F_UDF_FIELD_CATEGORY => $this->object->getFieldCategory(),
				self::F_CHECK_VALUE => $this->object->getCheckValue(),
				self::F_UDF_NEGATE_ID => $this->object->isNegated(),
				self::F_UDF_OPERATOR => $this->object->getOperator(),
				self::F_CHECK_RADIO => self::F_CHECK_TEXT
			];

			$definition = $this->object->getDefinition();

			//DHBW Spec
			if (self::isCustomUserFieldsHelperAvailable()) {
				$plugin = ilCustomUserFieldsHelper::getInstance()->getPluginForType($definition["field_type"]);
				if ($plugin instanceof ilUDFDefinitionPlugin) {
					$select_gui = $plugin->getFormPropertyForDefinition($definition);

					$check_values = $this->object->getCheckValues();
					foreach ($select_gui->getColumnDefinition() as $key => $name) {
						$array[self::F_CHECK_VALUE_MUL . $key] = $check_values[$key];
					}
				}
			}
		} else {
			$array = [
				self::F_UDF_FIELD_CATEGORY => UDFCheckUser::FIELD_CATEGORY
			];
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
					$definition = $this->object->getDefinition();

					//DHBW Spec
					if (self::isCustomUserFieldsHelperAvailable()) {
						$plugin = ilCustomUserFieldsHelper::getInstance()->getPluginForType($definition["field_type"]);
						if ($plugin instanceof ilUDFDefinitionPlugin) {
							$select_gui = $plugin->getFormPropertyForDefinition($definition, true, null, true);
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
			$this->object = UDFCheck::newInstance($this->getInput(self::F_UDF_FIELD_CATEGORY));
			$this->object->setFieldKey($this->getInput(self::F_UDF_FIELD_KEY . "_" . $this->object->getFieldCategory()));
			$this->object->setParentId($_GET[UserSettingsGUI::IDENTIFIER]);
			$this->object->create();
		}

		return $this->object->getId();
	}


	/**
	 *
	 */
	protected function addCommandButtons() {
		if (!$this->is_new) {
			$this->addCommandButton(UDFCheckGUI::CMD_UPDATE, self::plugin()->translate('form_button_update'));
		} else {
			$this->addCommandButton(UDFCheckGUI::CMD_CREATE, self::plugin()->translate('form_button_create'));
		}
		$this->addCommandButton(UDFCheckGUI::CMD_CANCEL, self::plugin()->translate('form_button_cancel'));
	}


	/**
	 * @return UDFCheck
	 */
	public function getObject() {
		return $this->object;
	}
}
