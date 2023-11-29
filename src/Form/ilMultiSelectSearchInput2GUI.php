<?php

namespace srag\Plugins\UserDefaults\Form;

use ilLegacyFormElementsUtil;
use ilMultiSelectInputGUI;
use ilTemplate;
use ilTemplateException;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Access\Courses;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use stdClass;

class ilMultiSelectSearchInput2GUI extends ilMultiSelectInputGUI {

	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected int $width = 300;
	protected int $height = 10;
	protected string $css_class = "";
	protected int $minimum_input_length = 0;
	protected string $ajax_link;
	protected string $link_to_object;
	protected ilTemplate $input_template;
	protected ilTemplate $tpl;
	protected mixed $multiple;
    protected ilUserDefaultsPlugin $pl;
    private \ILIAS\DI\UIServices $ui;
    private \ilObjUser $user;

    public function __construct(string $title, string $post_var, bool $multiple = true) {
        global $DIC;
		if (!str_ends_with($post_var, '[]')) {
			$post_var = $post_var . ($multiple === true ? '[]' : '');
		}
		parent::__construct($title, $post_var);
        $this->pl = ilUserDefaultsPlugin::getInstance();
        $this->ui = $DIC->ui();
        $this->user = $DIC->user();
        $this->multiple = $multiple;
        $template = $this->ui->mainTemplate();


        $template->addJavaScript(  $this->pl->getDirectory() . '/templates/default/multiple_select.js');
        $template->addJavaScript($this->pl->getDirectory() . '/lib/select2/select2.min.js');
        $template->addJavaScript($this->pl->getDirectory() . '/lib/select2/select2_locale_' . $this->user
				->getCurrentLanguage() . '.js');
        $template->addCss($this->pl->getDirectory() . '/lib/select2/select2.css');


        $this->setWidth(300);
	}


	public function checkInput(): bool
    {
		if ($this->getRequired()) {
		    if (($this->multiple && count($_POST[$this->getPostVar()]) == 0) || (!$this->multiple && $_POST[$this->getPostVar()] == '')) {
                $this->setAlert($this->pl->txt('msg_input_is_required'));
                return false;
            }
		}
		return true;
	}

	public function getValue(): array
    {
		$val = $this->value;
		if (is_array($val) || !$this->multiple) {
			return $val;
		} elseif (!$val) {
			return array();
		} else {
			return explode(',', (string)$val);
		}
	}

	public function getSubItems(): array
    {
		return array();
	}

	public function getContainerType(): string
    {
		return Courses::TYPE_CRS;
	}

    /**
     * @throws ilTemplateException
     */
    public function render(): string
    {
		$this->tpl = $this->getInputTemplate();
		$values = $this->getValueAsJson();
		$options = $this->getOptions();

        $this->tpl->setVariable('WIDTH', $this->getWidth());
        $this->tpl->setVariable('HEIGHT', $this->getHeight());
        $this->tpl->setVariable('POST_VAR', $this->getPostVar());
        $this->tpl->setVariable('ID', $this->stripLastStringOccurrence($this->getPostVar(), "[]"));
        $this->tpl->setVariable('ESCAPED_ID', $this->escapePostVar($this->getPostVar()));
        $this->tpl->setVariable('CSS_CLASS', $this->getCssClass());
        $this->tpl->setVariable('PLACEHOLDER', $this->pl->txt($this->getContainerType() . '_placeholder'));
        if ($this->getDisabled()) {
            $this->tpl->setVariable('ALL_DISABLED', 'disabled=\'disabled\'');
        }

        if ($this->multiple || !$this->getLinkToObject()) {
            $this->tpl->setVariable('LINK_HIDDEN', 'hidden');
        } else {
            $this->tpl->setVariable('LINK_TO_OBJECT', $this->getLinkToObject());
        }

        $config = new stdClass();
        $config->container_type = $this->getContainerType();
        $config->preload = json_decode($values);
        $config->minimum_input_length = $this->getMinimumInputLength();
        $config->id = $this->escapePostVar($this->getPostVar());
        $config->ajax_link = $this->getAjaxLink();
        $config->placeholder = $this->pl->txt($this->getContainerType() . '_placeholder');
        $config->multiple = (bool) $this->multiple;
        $this->ui->mainTemplate()->addOnLoadCode(
            'SrMultipleSelect.init("' . $config->id . '", ' . json_encode($config) . ');'
        );

        if ($options) {
			foreach ($options as $option_value => $option_text) {
				$this->tpl->setCurrentBlock('item');
				if ($this->getDisabled()) {
					$this->tpl->setVariable('DISABLED', ' disabled=\'disabled\'');
				}
				if (in_array($option_value, (array)$values)) {
					$this->tpl->setVariable('SELECTED', 'selected');
				}

				$this->tpl->setVariable('VAL', ilLegacyFormElementsUtil::prepareFormOutput($option_value));
				$this->tpl->setVariable('TEXT', $option_text);
				$this->tpl->parseCurrentBlock();
			}
		}

		return $this->tpl->get();
	}


	protected function getValueAsJson(): string
    {
		return json_encode(array());
	}

    public function getLinkToObject() : string
    {
        return $this->link_to_object;
    }


    public function setLinkToObject(string $link_to_object): void
    {
        $this->link_to_object = $link_to_object;
    }


	/**
	 * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
	 */
	public function setHeight(int $a_height): void
    {
		$this->height = $a_height;
	}


	public function getHeight(): int
    {
		return $this->height;
	}


	/**
	 * @deprecated setting inline style items from the controller is bad practice. please use the setClass together with an appropriate css class.
	 */
	public function setWidth(int $a_width): void
    {
		$this->width = $a_width;
	}

	public function getWidth(): int
    {
		return $this->width;
	}


	public function setCssClass(string $css_class): void {
		$this->css_class = $css_class;
	}


	public function getCssClass(): string
    {
		return $this->css_class;
	}


	public function setMinimumInputLength(int $minimum_input_length): void
    {
		$this->minimum_input_length = $minimum_input_length;
	}


	public function getMinimumInputLength(): int
    {
		return $this->minimum_input_length;
	}


	/**
	 * @param string $ajax_link setting the ajax link will lead to ignoration of the 'setOptions' function as the link given will be used to get the
	 */
	public function setAjaxLink(string $ajax_link): void
    {
		$this->ajax_link = $ajax_link;
	}

	public function getAjaxLink(): string
    {
		return $this->ajax_link;
	}

	public function setInputTemplate(ilTemplate $input_template): void
    {
		$this->input_template = $input_template;
	}


    /**
     * @return ilTemplate
     */
	public function getInputTemplate(): ilTemplate
    {
        return $this->pl->getTemplate('tpl.multiple_select.html');
	}


	/**
	 * This implementation might sound silly. But the multiple select input used parses the post vars differently if you use ajax. thus we have to do
	 * this stupid 'trick'. Shame on select2 project ;)
	 *
	 * @return string the real postvar.
	 */
	protected function searchPostVar(): string
    {
		if (str_ends_with($this->getPostVar(), '[]')) {
			return substr($this->getPostVar(), 0, - 2);
		} else {
			return $this->getPostVar();
		}
	}


	public function setValueByArray(array $a_values): void
    {
		$val = $a_values[$this->searchPostVar()];
		if (is_array($val)) {
			$val;
		} elseif (!$val) {
			$val = array();
		} else {
			$val = explode(',', $val);
		}
		$this->setValue($val);
	}


	protected function escapePostVar($postVar): array|string
    {
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
	private function stripLastStringOccurrence(string $text, string $string): string
    {
		$pos = strrpos($text, $string);
		if ($pos !== false) {
			$text = substr_replace($text, "", $pos, strlen($string));
		}

		return $text;
	}
}