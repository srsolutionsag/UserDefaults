<?php

namespace srag\Plugins\UserDefaults\Adapters\Ui\InputElements\FluxEcoSearchInputElement;

use ilFormPropertyGUI;
use ilSystemStyleException;
use ilTemplate;
use ilTemplateException;

class InputElement extends ilFormPropertyGUI
{
    protected array $value = [];
    protected string $dataSrc;
    private string $templatesPath;

    private function __construct(
        string $a_title = "",
        string $a_postvar = ""
    )
    {
        parent::__construct($a_title, $a_postvar);
    }

    public static function new(
        string $a_title,
        string $a_postvar,
        string $templatesPath,
        string $dataSrc
    ): self
    {
        $inputGUI = new InputElement($a_title, $a_postvar);
        $inputGUI->templatesPath = $templatesPath;
        $inputGUI->dataSrc = $dataSrc;
        return $inputGUI;
    }

    public function setValue(array $value): void
    {

        $this->value = $value;
    }

    public function getValue(): array
    {
        return $this->value;
    }

    public function getInput(): array
    {
        $post = $this->http->request()->getParsedBody();
        if ($post[$this->postvar] !== "") {
            return array_map('intval', explode(",", $post[$this->postvar]));
        }
        return [];
    }

    public function setValueByArray(array $a_values): void
    {
        $this->setValue([]);
        if (is_array($a_values[$this->postvar])) {
            $this->setValue($a_values[$this->postvar]);
        }
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
        if ($this->getRequired() && count($this->getValue()) === 0) {
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

        $tpl = new ilTemplate(__DIR__ . "/templates/tpl.html", true, true);
        $tpl->setCurrentBlock("flux_eco_ui_search_input_element");
        $tpl->setVariable("MODULE_NAME", $this->postvar);
        $tpl->setVariable("SEARCH_INPUT_CONF", '{
         dataSrc: "' . $this->dataSrc . '",
         name: "' . $this->postvar . '",
         selectedIds: "' . implode(",", $this->value) . '",
        }');
        $tpl->setVariable("JS_FILE_PATH", $this->templatesPath . "/../js");
        $tpl->parseCurrentBlock();
        return $tpl->get();
    }

    /**
     * @throws ilTemplateException
     * @throws ilSystemStyleException
     */
    public function insert(ilTemplate $a_tpl): void
    {
        $a_tpl->setCurrentBlock("prop_generic");
        $a_tpl->setVariable("PROP_GENERIC", $this->render());
        $a_tpl->parseCurrentBlock();
    }
}
