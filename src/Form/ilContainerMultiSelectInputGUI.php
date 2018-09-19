<?php

namespace srag\Plugins\UserDefaults\Form;

use ilUserDefaultsPlugin;
use srag\CustomInputGUIs\MultiSelectSearchInput2GUI;
use srag\Plugins\UserDefaults\UserSearch\usrdefObj;

/**
 * Class ilContainerMultiSelectInputGUI
 *
 * @package srag\Plugins\UserDefaults\Form
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class ilContainerMultiSelectInputGUI extends MultiSelectSearchInput2GUI {

	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	/**
	 * @var string
	 */
	protected $container_type = 'crs';


	/**
	 * @param string $container_type
	 * @param string $title
	 * @param        $post_var
	 */
	public function __construct($container_type, $title, $post_var) {
		$this->setContainerType($container_type);
		$this->setPlaceholder(self::plugin()->translate($this->getContainerType() . '_placeholder'));
		parent::__construct($title, $post_var);
	}


	/**
	 * @return string
	 */
	protected function getValueAsJson() {
		$query = "SELECT obj_id, title FROM " . usrdefObj::TABLE_NAME . " WHERE type = '" . $this->getContainerType() . "' AND " . self::dic()
				->database()->in("obj_id", $this->getValue(), false, "integer");
		$res = self::dic()->database()->query($query);
		$result = array();
		while ($row = self::dic()->database()->fetchAssoc($res)) {
			// If the value is blacklisted we don't return it.
			$result[] = array( "id" => $row['obj_id'], "text" => $row['title'] );
		}

		return json_encode($result);
	}


	/**
	 * @return mixed
	 */
	public function getValues() {
		return $this->value;
	}


	/**
	 * @param string $container_type
	 */
	public function setContainerType($container_type) {
		$this->container_type = $container_type;
	}


	/**
	 * @return string
	 */
	public function getContainerType() {
		return $this->container_type;
	}
}
