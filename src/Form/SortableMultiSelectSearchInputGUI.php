<?php

namespace srag\Plugins\UserDefaults\Form;

use ilDclGenericMultiInputGUI;
use ilException;
use ilUserDefaultsPlugin;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class SortableMultiSelectSearchInputGUI
 *
 * @package srag\Plugins\UserDefaults\Form
 *
 * @author  Theodor Truffer <tt@studer-raimann.ch>
 */
class SortableMultiSelectSearchInputGUI extends ilDclGenericMultiInputGUI
{

    use UserDefaultsTrait;
    const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;

    public function __construct(
        string $a_title = "",
        string $a_postvar = ""
    ) {
        global $DIC;
        parent::__construct($a_title, $a_postvar);
        $this->pl = ilUserDefaultsPlugin::getInstance();
    }


    /**
     * Insert property html
     *
     * @return void Size
     * @throws ilException
     */
    public function insert(\ilTemplate $a_tpl): void
    {
        global $DIC;
        $tpl = $DIC['tpl'];

        $output = "";
        // $tpl->addCss(self::plugin()->directory() . '/templates/default/multi_line_input.css');

        $output .= $this->render(0, true);

        if ($this->getMulti() && is_array($this->line_values) && count($this->line_values) > 0) {
            $counter = 0;
            foreach ($this->line_values as $i => $data) {
                $object = $this;
                $object->setValue($data);
                $output .= $object->render($i);
                $counter++;
            }
        } else {
            $output .= $this->render(1, true);
        }

        if ($this->getMulti()) {
            $output = '<div id="' . $this->getFieldId() . '" class="multi_line_input">' . $output . '</div>';
            $tpl->addJavascript($this->pl->getDirectory() . '/templates/default/generic_multi_line_input.js');
            $output .= '<script type="text/javascript">$("#' . $this->getFieldId() . '").multi_line_input('
                . json_encode($this->input_options) . ', '
                . json_encode(array('limit' => $this->limit, 'sortable' => $this->multi_sortable, 'locale' => $DIC->language()->getLangKey()))
                . ')</script>';
        }

        $a_tpl->setCurrentBlock("prop_generic");
        $a_tpl->setVariable("PROP_GENERIC", $output);
        $a_tpl->parseCurrentBlock();
    }
}