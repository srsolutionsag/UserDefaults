<?php

namespace srag\CustomInputGUIs\UserDefaults\NumberInputGUI;

use ilNumberInputGUI;
use ilTableFilterItem;
use srag\DIC\UserDefaults\DICTrait;

/**
 * Class NumberInputGUI
 *
 * @package srag\CustomInputGUIs\UserDefaults\NumberInputGUI
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class NumberInputGUI extends ilNumberInputGUI implements ilTableFilterItem {

	use DICTrait;


	/**
	 * Get input item HTML to be inserted into table filters
	 *
	 * @return string
	 */
	public function getTableFilterHTML()/*: string*/ {
		return $this->render();
	}
}
