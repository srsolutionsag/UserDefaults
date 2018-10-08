<?php

/* Copyright (c) 1998-2013 ILIAS open source, Extended GPL, see docs/LICENSE */

namespace srag\Plugins\UserDefaults\Form;

use ilExplorerSelectInputGUI;
use ilFormPropertyDispatchGUI;
use ilObject;
use ilPropertyFormGUI;
use ilRepositorySelector2InputGUI;
use ilRepositorySelectorExplorerGUI;
use ilUserDefaultsPlugin;
use srag\DIC\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * Class usrdefOrguSelectorInputGUI
 *
 * @package           srag\Plugins\UserDefaults\Form
 *
 * Select repository nodes
 *
 * @ilCtrl_IsCalledBy usrdefOrguSelectorInputGUI: ilFormPropertyDispatchGUI
 *
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
	protected $multi_nodes = false;
	/**
	 * @var ilRepositorySelectorExplorerGUI
	 */
	protected $explorer_gui;


	/**
	 * Constructor
	 *
	 * @param    string $a_title   Title
	 * @param    string $a_postvar Post Variable
	 */
	function __construct($a_title, $a_postvar, $a_multi = false) {
		$this->multi_nodes = $a_multi;
		$this->postvar = $a_postvar;

		$this->explorer_gui = new udfOrguSelectorExplorerGUI(array(
			ilPropertyFormGUI::class,
			ilFormPropertyDispatchGUI::class,
			ilRepositorySelector2InputGUI::class,
		), $this->getExplHandleCmd(), $this, "selectRepositoryItem", "root_id", "rep_exp_sel_" . $a_postvar);

		$this->explorer_gui->setSelectMode($a_postvar . "_sel", $this->multi_nodes);

		parent::__construct($a_title, $a_postvar, $this->explorer_gui, $this->multi_nodes);
		$this->setType("rep_select");
	}


	/**
	 * Set title modifier
	 *
	 * @param callable $a_val
	 */
	function setTitleModifier(callable $a_val) {
		$this->title_modifier = $a_val;
		if ($a_val != NULL) {
			$this->explorer_gui->setNodeContentModifier(function ($a_node) use ($a_val) {
				return $a_val($a_node["child"]);
			});
		} else {
			$this->explorer_gui->setNodeContentModifier(NULL);
		}
	}


	/**
	 * Get title modifier
	 *
	 * @return callable
	 */
	function getTitleModifier() {
		return $this->title_modifier;
	}


	/**
	 * Get title for node id (needs to be overwritten, if explorer is not a tree eplorer
	 *
	 * @param
	 *
	 * @return
	 */
	function getTitleForNodeId($a_id) {
		$c = $this->getTitleModifier();
		if (is_callable($c)) {
			return $c($a_id);
		}

		return ilObject::_lookupTitle(ilObject::_lookupObjId($a_id));
	}


	/**
	 * Handle explorer command
	 */
	function handleExplorerCommand() {
		if ($this->explorer_gui->handleCommand()) {
			//			exit;
		}
	}


	/**
	 * @return ilRepositorySelectorExplorerGUI
	 */
	function getExplorerGUI() {
		return $this->explorer_gui;
	}


	/**
	 * Get HTML
	 *
	 * @param
	 *
	 * @return
	 */
	function getHTML() {
		self::dic()->ctrl()->setParameterByClass(ilFormPropertyDispatchGUI::class, "postvar", $this->postvar);
		$html = parent::getHTML();
		self::dic()->ctrl()->setParameterByClass(ilFormPropertyDispatchGUI::class, "postvar", $_REQUEST["postvar"]);

		return $html;
	}


	/**
	 * Render item
	 */
	function render($a_mode = "property_form") {
		self::dic()->ctrl()->setParameterByClass(ilFormPropertyDispatchGUI::class, "postvar", $this->postvar);

		return parent::render($a_mode);
		self::dic()->ctrl()->setParameterByClass(ilFormPropertyDispatchGUI::class, "postvar", $_REQUEST["postvar"]);
	}
}
