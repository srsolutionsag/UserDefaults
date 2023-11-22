<?php

namespace srag\Plugins\UserDefaults\Form;

use ilGroupParticipants;
use ilObject;
use ilObjGroup;
use ilTemplateException;
use srag\Plugins\UserDefaults\Access\Courses;
use srag\Plugins\UserDefaults\UserSearch\usrdefObj;

class ilContainerMultiSelectInputGUI extends ilMultiSelectSearchInput2GUI {

	protected string $container_type = Courses::TYPE_CRS;
	protected bool $with_parent = false;
    protected bool $with_members = false;
    private \ilDBInterface $database;
    private \ilTree $repositoryTree;


    /**
     * @param string $container_type
     * @param string $title
     * @param string $post_var
     * @param bool $multiple
     * @param bool $with_parent
     * @param bool $with_members
     *
     */
	public function __construct(string $container_type, string $title, string $post_var, bool $multiple = true, bool $with_parent = false, bool $with_members = false) {
		global $DIC;
        $this->setContainerType($container_type);
		parent::__construct($title, $post_var, $multiple);
        $this->with_parent = $with_parent;
        $this->with_members = $with_members;
        $this->database = $DIC->database();
        $this->repositoryTree = $DIC->repositoryTree();
    }

	protected function getValueAsJson(): string
    {
        $result = array();
        if ($this->multiple) {
            $query = "SELECT obj_id, title FROM " . usrdefObj::TABLE_NAME . " WHERE type = '" . $this->getContainerType() . "' AND " .
                $this->database->in("obj_id", $this->getValue(), false, "integer");
            $res = $this->database->query($query);
            while ($row = $this->database->fetchAssoc($res)) {
                $title = $row["title"];
                if ($this->with_parent) {
                    $allReferences = ilObject::_getAllReferences($row["obj_id"]);
                    $ref_id = array_shift($allReferences);
                    $title = ilObject::_lookupTitle(ilObject::_lookupObjectId( $this->repositoryTree->getParentId($ref_id))) . ' » ' . $title;
                }
                $result[] = array( "id" => $row['obj_id'], "text" => $title );
            }
        } else {
            $query = "SELECT obj_id, title FROM " . usrdefObj::TABLE_NAME . " WHERE type = '" . $this->getContainerType() . "' AND " .
                $this->database->equals("obj_id", $this->getValue(),"integer");
            $res = $this->database->query($query);
            if ($row = $this->database->fetchAssoc($res)) {
                $title = $row["title"];
                $allReferences = ilObject::_getAllReferences($row["obj_id"]);
                if ($this->with_parent) {
                    $ref_id = array_shift($allReferences);
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

	public function getValues(): array
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