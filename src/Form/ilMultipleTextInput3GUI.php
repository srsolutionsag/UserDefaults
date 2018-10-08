<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\UserDefaults\Form;

use ilSubEnabledFormPropertyGUI;
use ilTemplate;
use ilUserDefaultsPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class ilMultipleTextInput3GUI
 *
 * @package srag\Plugins\UserDefaults\Form
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version $Id:
 */
class ilMultipleTextInput3GUI extends ilSubEnabledFormPropertyGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	/**
	 * @var array
	 */
	protected $values;
	/**
	 * @var string
	 */
	protected $placeholder;
	/**
	 * @var bool
	 */
	protected $disableOldFields;


	/**
	 * @param string $title
	 * @param string $post_var
	 * @param        $placeholder
	 */
	public function __construct($title, $post_var, $placeholder) {
		parent::__construct($title, $post_var);
		$this->placeholder = $placeholder;
	}


	/**
	 * @return string
	 */
	public function getHtml() {
		$tpl = self::plugin()->template("tpl.multiple_input.html");
		$tpl = $this->buildHTML($tpl);

		$this->checkInput();

		return $tpl->get();
	}


	/**
	 * @param ilTemplate $tpl
	 *
	 * @return ilTemplate
	 */
	protected function buildHTML($tpl) {
		$tpl->setCurrentBlock("title");
		$tpl->setVariable("CSS_PATH", self::plugin()->getPluginObject()->getStyleSheetLocation("content.css"));
		$tpl->setVariable("X_IMAGE_PATH", self::plugin()->getPluginObject()->getImagePath("x_image.png"));
		$tpl->setVariable("PLACEHOLDER", $this->placeholder);
		$tpl->setVariable("POSTVAR", $this->getPostVar());
		$tpl->setVariable("NEW_OPTION", $this->getPostVar());
		$tpl->parseCurrentBlock();

		$tpl->touchBlock("lvo_options_start");
		$tpl->setVariable("POSTVAR2", $this->getPostVar());
		$new = 0;
		foreach ($this->values as $id => $value) {
			if ($value) {
				$tpl->setCurrentBlock("lvo_option");
				$tpl->setVariable("OPTION_ID", $this->getPostVar() . "[" . $id . "]");
				$tpl->setVariable("NEW_OPTION", $new);
				if (substr($id, 0, 3) == "new") {
					$new ++;
				}
				$tpl->setVariable("OPTION_VALUE", $value);
				$tpl->setVariable("OPTION_CLASS", "lvo_option");
				$tpl->setVariable("PLACEHOLDER_CLASS", "");
				$tpl->setVariable("PLACEHOLDER", "");
				$tpl->setVariable("X_DISPLAY", "float");
				$tpl->setVariable("DISABLED", "disabled");
				$tpl->setVariable("X_IMAGE_PATH", self::plugin()->getPluginObject()->getImagePath("x_image.png"));
				$tpl->parseCurrentBlock();
			}
		}

		$tpl->setCurrentBlock("lvo_option");
		$tpl->setVariable("OPTION_ID", $this->getPostVar() . "[new" . $new . "]");
		$tpl->setVariable("NEW_OPTION", $new);
		$tpl->setVariable("OPTION_TITLE", "");
		$tpl->setVariable("OPTION_CLASS", "lvo_new_option");
		$tpl->setVariable("PLACEHOLDER", "placeholder = '" . $this->placeholder . "'");
		$tpl->setVariable("PLACEHOLDER_CLASS", "placeholder");
		$tpl->setVariable("X_IMAGE_PATH", self::plugin()->getPluginObject()->getImagePath("x_image.png"));
		$tpl->setVariable("X_DISPLAY", "none");
		$tpl->parseCurrentBlock();

		$tpl->touchBlock("lvo_options_end");

		return $tpl;
	}


	/**
	 * @param mixed $value
	 */
	function setValueByArray($value) {
		$cleaned_values = array();
		foreach ($value[$this->getPostVar()] as $v) {
			if ($v) {
				$cleaned_values[] = $v;
			}
		}

		foreach ($this->getSubItems() as $item) {
			$item->setValueByArray($value);
		}
		$this->values = is_array($cleaned_values) ? $cleaned_values : array();
	}


	/**
	 * @param boolean $disableOldFields
	 */
	public function setDisableOldFields($disableOldFields) {
		$this->disableOldFields = $disableOldFields;
	}


	/**
	 * @return boolean
	 */
	public function getDisableOldFields() {
		return $this->disableOldFields;
	}


	/**
	 * @param ilTemplate $template
	 */
	public function insert(&$template) {
		$template->setCurrentBlock("prop_custom");
		$template->setVariable("CUSTOM_CONTENT", $this->getHtml());
		$template->parseCurrentBlock();
	}


	/**
	 * @return bool
	 */
	public function checkInput() {
		return true;
	}


	/**
	 * @return array
	 */
	public function getValues() {
		return $this->values;
	}


	/**
	 * @return array
	 */
	public function getValue() {
		return $this->values;
	}
}
