<?php

namespace srag\Plugins\UserDefaults\Form;

use ilCtrlException;
use ilExplorerSelectInputGUI;
use ilFormPropertyDispatchGUI;
use ilObject;
use ilPropertyFormGUI;
use ilRepositorySelector2InputGUI;
use ilRepositorySelectorExplorerGUI;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * @ilCtrl_IsCalledBy usrdefOrguSelectorInputGUI: ilFormPropertyDispatchGUI
 */
class usrdefOrguSelectorInputGUI extends ilExplorerSelectInputGUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	/**
	 * @var callable
	 */
	protected $title_modifier = NULL;
	/**
	 * @var bool
	 */
	protected bool $multi_nodes = false;
	/**
	 * @var ilRepositorySelectorExplorerGUI
	 */
	protected \ilExplorerBaseGUI $explorer_gui;


	function __construct(string $a_title, string $a_postvar, bool $a_multi = false) {
        global $DIC;
		$this->multi_nodes = $a_multi;
		$this->postvar = $a_postvar;

        $this->ctrl = $DIC->ctrl();

		$this->explorer_gui = new udfOrguSelectorExplorerGUI(array(
			ilPropertyFormGUI::class,
			ilFormPropertyDispatchGUI::class,
			ilRepositorySelector2InputGUI::class,
		), $this->getExplHandleCmd(), $this, "selectRepositoryItem", "root_id", "rep_exp_sel_" . $a_postvar);

		$this->explorer_gui->setSelectMode($a_postvar . "_sel", $this->multi_nodes);

		parent::__construct($a_title, $a_postvar, $this->explorer_gui, $this->multi_nodes);
		$this->setType("rep_select");
	}


	function setTitleModifier(callable $a_val): void
    {
		$this->title_modifier = $a_val;
		if ($a_val != NULL) {
			$this->explorer_gui->setNodeContentModifier(function ($a_node) use ($a_val) {
				return $a_val($a_node["child"]);
			});
		} else {
			$this->explorer_gui->setNodeContentModifier(NULL);
		}
	}

	function getTitleModifier(): ?callable
    {
		return $this->title_modifier;
	}

	function getTitleForNodeId($a_id): string
    {
		$c = $this->getTitleModifier();
		if (is_callable($c)) {
			return $c($a_id);
		}

		return ilObject::_lookupTitle(ilObject::_lookupObjId($a_id));
	}


	function handleExplorerCommand(): void
    {
		if ($this->explorer_gui->handleCommand()) {
			//			exit;
		}
	}

	function getExplorerGUI(): \ilExplorerBaseGUI|udfOrguSelectorExplorerGUI|ilRepositorySelectorExplorerGUI
    {
		return $this->explorer_gui;
	}

    /**
     * @throws ilCtrlException
     */
    function getHTML(): string
    {
        $this->ctrl->setParameterByClass(ilFormPropertyDispatchGUI::class, "postvar", $this->postvar);
		$html = parent::getTableFilterHTML();
        $this->ctrl->setParameterByClass(ilFormPropertyDispatchGUI::class, "postvar", $_REQUEST["postvar"]);

		return $html;
	}

    /**
     * @throws ilCtrlException
     */
    function render($a_mode = "property_form"): string
    {
        $this->ctrl->setParameterByClass(ilFormPropertyDispatchGUI::class, "postvar", $this->postvar);

		return parent::render($a_mode);
	}
}