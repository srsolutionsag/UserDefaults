<?php

class FluxEcoSearchInputGUI extends ilFormPropertyGUI implements ilTableFilterItem
{
    protected array $options = [];
    protected string $value = "";
    protected bool $select_all = false;
    protected bool $selected_first = false;
    private int $width = 160;
    private int $height = 100;
    protected string $widthUnit = 'px';
    protected string $heightUnit = 'px';
    protected array $custom_attributes = [];
    protected string $dataSrc = "";
    private ilUserDefaultsPlugin $pl;

    public function __construct(
        string $a_title = "",
        string $a_postvar = ""
    )
    {
        global $DIC;

        //$main_tpl = $DIC->ui()->mainTemplate()->setCurrentBlock()

        $this->lng = $DIC->language();
        parent::__construct($a_title, $a_postvar);
        $this->setType("multi_select");
    }

    public function setDataSrc(string $dataSrc) {
        $this->dataSrc = $dataSrc;
    }

    public function setWidth(int $a_width): void
    {
        $this->width = $a_width;
    }

    public function getWidth(): int
    {
        return $this->width;
    }

    public function setHeight(int $a_height): void
    {
        $this->height = $a_height;
    }

    public function getHeight(): int
    {
        return $this->height;
    }

    /**
     * @param array<string,string> $a_options Options. Array ("value" => "option_text")
     */
    public function setOptions(array $a_options): void
    {
        $this->options = $a_options;
    }

    /**
     * @return array<string,string>
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @param string
     */
    public function setValue($value): void
    {

        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string[]
     */
    public function setValueByArray(array $a_values): void
    {
        $this->setValue($a_values[$this->getPostVar()] ?? "");
    }

    public function enableSelectAll(bool $a_value): void
    {
        $this->select_all = $a_value;
    }

    public function enableSelectedFirst(bool $a_value): void
    {
        $this->selected_first = $a_value;
    }

    public function checkInput(): bool
    {
        $lng = $this->lng;
        $values = array_map("intval", explode(",", $this->getValue()));;
        if ($this->getRequired() && count($values) == 0) {
            $this->setAlert($lng->txt("msg_input_is_required"));
            return false;
        }

        return true;
    }


    /**
     * @throws ilSystemStyleException
     * @throws ilTemplateException
     */
    public function render(): string
    {
        $lng = $this->lng;

        $tpl = new ilTemplate(__DIR__."/tpl.html", true, true);
        $tpl->setCurrentBlock("input");
        $tpl->setVariable("DATA_SRC", $this->dataSrc);
        $tpl->setVariable("NAME", $this->postvar);
        $tpl->parseCurrentBlock();

        $values = $this->getValue();

        $options = $this->getOptions();
        if ($options) {
            if ($this->select_all) {
                // enable select all toggle
                $tpl->setCurrentBlock("item");
                $tpl->setVariable("VAL", "");
                $tpl->setVariable("ID_VAL", ilLegacyFormElementsUtil::prepareFormOutput("all__toggle"));
                $tpl->setVariable("IID", $this->getFieldId());
                $tpl->setVariable("TXT_OPTION", "<em>" . $lng->txt("select_all") . "</em>");
                $tpl->setVariable("POST_VAR", $this->getPostVar());
                $tpl->parseCurrentBlock();

                $tpl->setVariable("TOGGLE_FIELD_ID", $this->getFieldId());
                $tpl->setVariable("TOGGLE_ALL_ID", $this->getFieldId() . "_all__toggle");
                $tpl->setVariable("TOGGLE_ALL_CBOX_ID", $this->getFieldId() . "_");
            }

            if ($this->selected_first) {
                // move selected values to top
                $tmp_checked = $tmp_unchecked = array();
                foreach ($options as $option_value => $option_text) {
                    if (in_array($option_value, $values)) {
                        $tmp_checked[$option_value] = $option_text;
                    } else {
                        $tmp_unchecked[$option_value] = $option_text;
                    }
                }
                $options = $tmp_checked + $tmp_unchecked;
                unset($tmp_checked);
                unset($tmp_unchecked);
            }

            foreach ($options as $option_value => $option_text) {
                $tpl->setCurrentBlock("item");
                if ($this->getDisabled()) {
                    $tpl->setVariable(
                        "DISABLED",
                        " disabled=\"disabled\""
                    );
                }
                if (in_array($option_value, $values)) {
                    $tpl->setVariable(
                        "CHECKED",
                        " checked=\"checked\""
                    );
                }

                $tpl->setVariable("VAL", ilLegacyFormElementsUtil::prepareFormOutput($option_value));
                $tpl->setVariable("ID_VAL", ilLegacyFormElementsUtil::prepareFormOutput($option_value));
                $tpl->setVariable("IID", $this->getFieldId());
                $tpl->setVariable("TXT_OPTION", $option_text);
                $tpl->setVariable("POST_VAR", $this->getPostVar());
                $tpl->parseCurrentBlock();
            }
        }

        $tpl->setVariable("ID", $this->getFieldId());
        $tpl->setVariable("CUSTOM_ATTRIBUTES", implode(' ', $this->getCustomAttributes()));

        if ($this->getWidth()) {
            $tpl->setVariable("WIDTH", $this->getWidth() . ($this->getWidthUnit() ?: ''));
        }
        if ($this->getHeight()) {
            $tpl->setVariable("HEIGHT", $this->getHeight() . ($this->getHeightUnit() ?: ''));
        }

        return $tpl->get();
    }

    public function insert(ilTemplate $a_tpl): void
    {
        $a_tpl->setCurrentBlock("prop_generic");
        $a_tpl->setVariable("PROP_GENERIC", $this->render());
        $a_tpl->parseCurrentBlock();
    }

    public function getTableFilterHTML(): string
    {
        $html = $this->render();
        return $html;
    }

    public function getCustomAttributes(): array
    {
        return $this->custom_attributes;
    }

    public function setCustomAttributes(array $custom_attributes): void
    {
        $this->custom_attributes = $custom_attributes;
    }


    public function addCustomAttribute(string $custom_attribute): void
    {
        $this->custom_attributes[] = $custom_attribute;
    }

    public function getWidthUnit(): string
    {
        return $this->widthUnit;
    }

    public function setWidthUnit(string $widthUnit): void
    {
        $this->widthUnit = $widthUnit;
    }

    public function getHeightUnit(): string
    {
        return $this->heightUnit;
    }

    public function setHeightUnit(string $heightUnit): void
    {
        $this->heightUnit = $heightUnit;
    }

    public function unserializeData(string $a_data): void
    {
        $data = unserialize($a_data);

        if (is_array($data)) {
            $this->setValue($data);
        } else {
            $this->setValue([]);
        }
    }
}
