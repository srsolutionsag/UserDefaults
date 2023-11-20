<?php
/* Copyright (c) 1998-2009 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\UserDefaults\Form;

use ilSubEnabledFormPropertyGUI;
use ilTemplate;
use ilTemplateException;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

class ilMultipleTextInput3GUI extends ilSubEnabledFormPropertyGUI {
    use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected array $values;
	protected string $placeholder;
	protected bool $disableOldFields;
    private ilUserDefaultsPlugin $pl;

    public function __construct(string $title, string $post_var, string $placeholder) {
		parent::__construct($title, $post_var);
        $this->pl = ilUserDefaultsPlugin::getInstance();

		$this->placeholder = $placeholder;
	}


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    public function getHtml(): string
    {
		$tpl = self::plugin()->template("tpl.multiple_input.html");
		$tpl = $this->buildHTML($tpl);

		return self::output()->getHTML($tpl);
	}


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    protected function buildHTML(ilTemplate $tpl): ilTemplate
    {
		$tpl->setCurrentBlock("title");
		$tpl->setVariable("CSS_PATH",  $this->pl->getStyleSheetLocation("content.css"));
		$tpl->setVariable("X_IMAGE_PATH",  $this->pl->getImagePath("x_image.png"));
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
				if (str_starts_with($id, "new")) {
					$new ++;
				}
				$tpl->setVariable("OPTION_VALUE", $value);
				$tpl->setVariable("OPTION_CLASS", "lvo_option");
				$tpl->setVariable("PLACEHOLDER_CLASS", "");
				$tpl->setVariable("PLACEHOLDER", "");
				$tpl->setVariable("X_DISPLAY", "float");
				$tpl->setVariable("DISABLED", "disabled");
				$tpl->setVariable("X_IMAGE_PATH", $this->pl->getImagePath("x_image.png"));
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
		$tpl->setVariable("X_IMAGE_PATH", $this->pl->getImagePath("x_image.png"));
		$tpl->setVariable("X_DISPLAY", "none");
		$tpl->parseCurrentBlock();

		$tpl->touchBlock("lvo_options_end");

		return $tpl;
	}

	function setValueByArray(mixed $value): void
    {
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

	public function setDisableOldFields(bool $disableOldFields): void {
		$this->disableOldFields = $disableOldFields;
	}

	public function getDisableOldFields(): bool
    {
		return $this->disableOldFields;
	}


    /**
     * @throws DICException
     * @throws ilTemplateException
     */
    public function insert(ilTemplate &$template): void
    {
		$template->setCurrentBlock("prop_custom");
		$template->setVariable("CUSTOM_CONTENT", $this->getHtml());
		$template->parseCurrentBlock();
	}

	public function checkInput(): bool
    {
		return true;
	}

	public function getValues(): array
    {
		return $this->values;
	}

	public function getValue(): array
    {
		return $this->values;
	}
}
