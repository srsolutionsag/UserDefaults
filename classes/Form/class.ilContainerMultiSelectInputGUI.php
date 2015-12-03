<?php

require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/Form/class.ilMultiSelectSearchInput2GUI.php');

/**
 * Class ilContainerMultiSelectInputGUI
 *
 * @author Oskar Truffer <ot@studer-raimann.ch>
 * @author Fabian Schmid <fs@studer-raimann.ch>
 */
class ilContainerMultiSelectInputGUI extends ilMultiSelectSearchInput2GUI {

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
		parent::__construct($title, $post_var);
	}

	/**
	 * @return string
	 */
	protected function getValueAsJson() {
		global $ilDB;
		$query = "SELECT obj_id, title FROM object_data WHERE type = '" . $this->getContainerType()
			. "' AND " . $ilDB->in("obj_id", $this->getValue(), false, "integer");
		$res = $ilDB->query($query);
		$result = array();
		while ($row = $ilDB->fetchAssoc($res)) {
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