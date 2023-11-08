<?php

namespace srag\Plugins\UserDefaults\Form;

use ilGroupParticipants;
use ilObject;
use ilObjGroup;
use ilTemplateException;
use srag\DIC\UserDefaults\Exception\DICException;
use srag\Plugins\UserDefaults\Access\Courses;
use srag\Plugins\UserDefaults\UserSearch\usrdefObj;

/**
 * Class ilContainerMultiSelectInputGUI
 *
 * @package srag\Plugins\UserDefaults\Form
 *
 * @author  Oskar Truffer <ot@studer-raimann.ch>
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 */
class ilContainerMultiSelectInputGUI extends ilMultiSelectSearchInput2GUI {

	protected string $container_type = Courses::TYPE_CRS;
	protected bool $with_parent = false;
    protected bool $with_members = false;


    /**
     * @param string $container_type
     * @param string $title
     * @param        $post_var
     * @param bool   $multiple
     *
     * @param bool   $with_parent
     *
     * @param bool   $with_members
     *
     * @throws DICException
     * @throws ilTemplateException
     */
	public function __construct($container_type, $title, $post_var, $multiple = true, $with_parent = false, $with_members = false) {
		$this->setContainerType($container_type);
		parent::__construct($title, $post_var, $multiple);
        $this->with_parent = $with_parent;
        $this->with_members = $with_members;
    }


    /**
     * @return string
     * @throws DICException
     */
	protected function getValueAsJson(): string
    {
        $result = array();
        if ($this->multiple) {
            $query = "SELECT obj_id, title FROM " . usrdefObj::TABLE_NAME . " WHERE type = '" . $this->getContainerType() . "' AND " .
                self::dic()->database()->in("obj_id", $this->getValue(), false, "integer");
            $res = self::dic()->database()->query($query);
            while ($row = self::dic()->database()->fetchAssoc($res)) {
                $title = $row["title"];
                if ($this->with_parent) {
                    $ref_id = array_shift(ilObject::_getAllReferences($row["obj_id"]));
                    $title = ilObject::_lookupTitle(ilObject::_lookupObjectId(self::dic()->repositoryTree()->getParentId($ref_id))) . ' » ' . $title;
                }
                $result[] = array( "id" => $row['obj_id'], "text" => $title );
            }
        } else {
            $query = "SELECT obj_id, title FROM " . usrdefObj::TABLE_NAME . " WHERE type = '" . $this->getContainerType() . "' AND " .
                self::dic()->database()->equals("obj_id", $this->getValue(),"integer");
            $res = self::dic()->database()->query($query);
            if ($row = self::dic()->database()->fetchAssoc($res)) {
                $title = $row["title"];
                if ($this->with_parent) {
                    $ref_id = array_shift(ilObject::_getAllReferences($row["obj_id"]));
                    $title = ilObject::_lookupTitle(ilObject::_lookupObjectId(self::dic()->repositoryTree()->getParentId($ref_id))) . ' » ' . $title;
                }
                if ($this->with_members && $this->getContainerType() == 'grp') {
                    $group = new ilObjGroup($row['obj_id'], false);
                    $part = ilGroupParticipants::_getInstanceByObjId($row['obj_id']);
                    $title = $title . ' (' . $part->getCountMembers() . '/' . ($group->getMaxMembers() == 0 ? '-' : $group->getMaxMembers()) . ')';
                }
                $result = ["id" => $row['obj_id'], "text" => $title];
            }
        }

		return json_encode($result);
	}

	public function getValues(): mixed
    {
		return $this->value;
	}


	public function setContainerType(string $container_type): void
    {
		$this->container_type = $container_type;
	}

	public function getContainerType(): string
    {
		return $this->container_type;
	}
}
