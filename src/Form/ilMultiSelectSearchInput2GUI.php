<?php

namespace srag\Plugins\UserDefaults\Form;

use ilMultiSelectInputGUI;
use ilTemplate;
use ilUserDefaultsPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use srDefaultAccessChecker;

/**
 * Class ilMultiSelectSearchInput2GUI
 *
 * @package srag\Plugins\UserDefaults\Form
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 */
class ilMultiSelectSearchInput2GUI extends ilMultiSelectInputGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	/**
	 * @var string
	 */
	protected $width;
	/**
	 * @var string
	 */
	protected $height;
	/**
	 * @var string
	 */
	protected $css_class;
	/**
	 * @var int
	 */
	protected $minimum_input_length = 0;
	/**
	 * @var string
	 */
	protected $ajax_link;
	/**
	 * @var ilTemplate
	 */
	protected $input_template;
	/**
	 * @var ilTemplate
	 */
	protected $tpl;


	/**
	 * @param string $title
	 * @param string $post_var
	 */
	public function __construct($title, $post_var) {
		if (substr($post_var, - 2) != '[]') {
			$post_var = $post_var . '[]';
		}
		parent::__construct($title, $post_var);

		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/lib/select2/select2.min.js');
		self::dic()->mainTemplate()->addJavaScript(self::plugin()->directory() . '/lib/select2/select2_locale_' . self::dic()->user()
				->getCurrentLanguage() . '.js');
		self::dic()->mainTemplate()->addCss(self::plugin()->directory() . '/lib/select2/select2.css');
		$this->setInputTemplate(self::plugin()->template('tpl.multiple_select.html'));
		$this->setWidth('300px');
	}


	/**
	 * @return bool
	 */
	public function checkInput() {
		if ($this->getRequired() && count($this->getValue()) == 0) {
			$this->setAlert(self::dic()->language()->txt('msg_input_is_required'));

			return false;
		}

		return true;
	}


	/**
	 * @return array
	 */
	public function getValue() {
		$val = parent::getValue();
		if (is_array($val)) {
			return $val;
		} elseif (!$val) {
			return array();
		} else {
			return explode(',', $val);
		}
	}


	/**
	 * @return array
	 */
	public function getSubItems() {
		return array();
	}


	public function getContainerType() {
		return 'crs';
	}


	/**
	 * @return string
	 */
	public function render() {
		$this->tpl = $this->getInputTemplate();
		$values = $this->getValueAsJson();
		$options = $this->getOptions();

		$this->tpl->setVariable('POST_VAR', $this->getPostVar());
		$this->tpl->setVariable('ID', $this->stripLastStringOccurrence($this->getPostVar(), "[]"));
		$this->tpl->setVariable('ESCAPED_ID', $this->escapePostVar($this->getPostVar()));
		$this->tpl->setVariable('WIDTH', $this->getWidth());
		$this->tpl->setVariable('PRELOAD', $values);
		$this->tpl->setVariable('HEIGHT', $this->getHeight());
		$this->tpl->setVariable('PLACEHOLDER', self::plugin()->translate($this->getContainerType() . '_placeholder'));
		$this->tpl->setVariable('MINIMUM_INPUT_LENGTH', $this->getMinimumInputLength());
		$this->tpl->setVariable('CONTAINER_TYPE', $this->getContainerType());
		$this->tpl->setVariable('Class', $this->getCssClass());

		if (isset($this->ajax_link)) {
			$this->tpl->setVariable('AJAX_LINK', $this->getAjaxLink());
		}

		if ($this->getDisabled()) {
			$this->tpl->setVariable('ALL_DISABLED', 'disabled=\'disabled\'');
		}

		if ($options) {
			foreach ($options as $option_value => $option_text) {
				$this->tpl->setCurrentBlock('item');
				if ($this->getDisabled()) {
					$this->tpl->setVariable('DISABLED', ' disabled=\'disabled\'');
				}
				if (in_array($option_value, $values)) {
					$this->tpl->setVariable('SELECTED', 'selected');
				}

				$this->tpl->setVariable('VAL', ilUtil::prepareFormOutput($option_value));
				$this->tpl->setVariable('TEXT', $option_text);
				$this->tpl->parseCurrentBlock();
			}
		}

		return $this->tpl->get();
	}


	/**
	 * @return string
	 */
	protected function getValueAsJson() {
		return json_encode(array());
	}


	/**
	 * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
	 *
	 * @param string $height
	 */
	public function setHeight($height) {
		$this->height = $height;
	}


	/**
	 * @return string
	 */
	public function getHeight() {
		return $this->height;
	}


	/**
	 * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
	 *
	 * @param string $width
	 */
	public function setWidth($width) {
		$this->width = $width;
	}


	/**
	 * @return string
	 */
	public function getWidth() {
		return $this->width;
	}


	/**
	 * @param string $css_class
	 */
	public function setCssClass($css_class) {
		$this->css_class = $css_class;
	}


	/**
	 * @return string
	 */
	public function getCssClass() {
		return $this->css_class;
	}


	/**
	 * @param int $minimum_input_length
	 */
	public function setMinimumInputLength($minimum_input_length) {
		$this->minimum_input_length = $minimum_input_length;
	}


	/**
	 * @return int
	 */
	public function getMinimumInputLength() {
		return $this->minimum_input_length;
	}


	/**
	 * @param string $ajax_link setting the ajax link will lead to ignoration of the 'setOptions' function as the link given will be used to get the
	 */
	public function setAjaxLink($ajax_link) {
		$this->ajax_link = $ajax_link;
	}


	/**
	 * @return string
	 */
	public function getAjaxLink() {
		return $this->ajax_link;
	}


	/**
	 * @param srDefaultAccessChecker $access_checker
	 */
	public function setAccessChecker($access_checker) {
		$this->access_checker = $access_checker;
	}


	/**
	 * @return srDefaultAccessChecker
	 */
	public function getAccessChecker() {
		return $this->access_checker;
	}


	/**
	 * @param ilTemplate $input_template
	 */
	public function setInputTemplate($input_template) {
		$this->input_template = $input_template;
	}


	/**
	 * @return ilTemplate
	 */
	public function getInputTemplate() {
		return $this->input_template;
	}


	/**
	 * This implementation might sound silly. But the multiple select input used parses the post vars differently if you use ajax. thus we have to do
	 * this stupid 'trick'. Shame on select2 project ;)
	 *
	 * @return string the real postvar.
	 */
	protected function searchPostVar() {
		if (substr($this->getPostVar(), - 2) == '[]') {
			return substr($this->getPostVar(), 0, - 2);
		} else {
			return $this->getPostVar();
		}
	}


	/**
	 * @param array $array
	 */
	public function setValueByArray($array) {
		$val = $array[$this->searchPostVar()];
		if (is_array($val)) {
			$val;
		} elseif (!$val) {
			$val = array();
		} else {
			$val = explode(',', $val);
		}
		$this->setValue($val);
	}


	protected function escapePostVar($postVar) {
		$postVar = $this->stripLastStringOccurrence($postVar, "[]");
		$postVar = str_replace("[", '\\\\[', $postVar);
		$postVar = str_replace("]", '\\\\]', $postVar);

		return $postVar;
	}


	/**
	 * @param string $text
	 * @param string $string
	 *
	 * @return string
	 */
	private function stripLastStringOccurrence($text, $string) {
		$pos = strrpos($text, $string);
		if ($pos !== false) {
			$text = substr_replace($text, "", $pos, strlen($string));
		}

		return $text;
	}
}
