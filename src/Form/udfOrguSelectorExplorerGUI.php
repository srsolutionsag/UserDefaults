<?php

namespace srag\Plugins\UserDefaults\Form;

use ilContainerSorting;
use ilCtrlException;
use ilObject;
use ilObjectDefinition;
use ilTreeExplorerGUI;
use ilUserDefaultsPlugin;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

class udfOrguSelectorExplorerGUI extends ilTreeExplorerGUI
{
    use DICTrait;
    use UserDefaultsTrait;

    /**
     * @var string
     */
    public $selection_gui;
    /**
     * @var string
     */
    public $selection_cmd;
    /**
     * @var string
     */
    public $selection_par;
    public $selectable_types;
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    protected array $type_grps = [];
    protected array $session_materials = [];
    /**
     * @var mixed
     */
    protected $highlighted_node = null;
    protected array $clickable_types = [];
    /**
     * @var callable
     */
    protected $nc_modifier = null;
    private \ilAccessHandler $access;
    private ilObjectDefinition $objDefinition;

    public function __construct(
        ?object $a_parent_obj,
        string $a_parent_cmd,
        ?object $a_selection_gui = null,
        string $a_selection_cmd = "selectObject",
        string $a_selection_par = "sel_ref_id",
        string $a_id = "rep_exp_sel"
    ) {
        global $DIC;
        if (is_null($a_selection_gui)) {
            $a_selection_gui = $a_parent_obj;
        }
        $this->lng = $DIC->language();
        $this->ctrl = $DIC->ctrl();
        $this->access = $DIC->access();
        $this->objDefinition = $DIC["objDefinition"];

        $repositoryTree = $DIC->repositoryTree();
        $this->selection_gui = is_object($a_selection_gui) ? strtolower($a_selection_gui::class) : strtolower(
            (string) $a_selection_gui
        );
        $this->selection_cmd = $a_selection_cmd;
        $this->selection_par = $a_selection_par;
        parent::__construct($a_id, $a_parent_obj, $a_parent_cmd, $repositoryTree);
        $this->setSkipRootNode(true);
        $this->setAjax(true);
        $this->setOrderField("title");

        $this->setTypeWhiteList(['orgu']);
        $this->setPathOpen(56);
    }

    /**
     * Set node content modifier
     */
    public function setNodeContentModifier(callable $a_val): void
    {
        $this->nc_modifier = $a_val;
    }

    public function getNodeContentModifier(): ?callable
    {
        return $this->nc_modifier;
    }

    /**
     * Get node content
     *
     * @param array $a_node node data
     * @return string content
     */
    public function getNodeContent($a_node): string
    {
        $c = $this->getNodeContentModifier();
        if (is_callable($c)) {
            return $c($a_node);
        }

        $title = $a_node["title"];
        if ($a_node["child"] == $this->getNodeId($this->getRootNode()) && $title == "ILIAS") {
            return $this->lng->txt("repository");
        }

        return $title;
    }

    /**
     * Get node icon
     *
     * @param array $a_node node data
     * @return string icon path
     */
    public function getNodeIcon($a_node): string
    {
        $obj_id = ilObject::_lookupObjId($a_node["child"]);

        return ilObject::_getIcon($obj_id, "tiny", $a_node["type"]);
    }

    /**
     * Get node icon alt text
     *
     * @param array $a_node node data
     * @return string alt text
     */
    public function getNodeIconAlt($a_node): string
    {
        if ($a_node["child"] == $this->getNodeId($this->getRootNode())) {
            $title = $a_node["title"];
            if ($title == "ILIAS") {
                $title = $this->lng->txt("repository");
            }

            return $this->lng->txt("icon") . " " . $title;
        }

        return parent::getNodeIconAlt($a_node);
    }

    /**
     * Is node highlighted?
     *
     * @param mixed $a_node node object/array
     * @return boolean node visible true/false
     */
    public function isNodeHighlighted($a_node): bool
    {
        if ($this->getHighlightedNode()) {
            return $this->getHighlightedNode() == $a_node["child"];
        }
        return $a_node["child"] == $_GET["ref_id"]
            || ($_GET["ref_id"] == "" && $a_node["child"] == $this->getNodeId($this->getRootNode()));
    }

    /**
     * Get href for node
     *
     * @param mixed $a_node node object/array
     * @return string href attribute
     * @throws ilCtrlException
     */
    public function getNodeHref($a_node): string
    {
        if ($this->select_postvar === "") {
            $this->ctrl->setParameterByClass($this->selection_gui, $this->selection_par, $a_node["child"]);
            $link = $this->ctrl->getLinkTargetByClass($this->selection_gui, $this->selection_cmd);
            $this->ctrl->setParameterByClass($this->selection_gui, $this->selection_par, "");
        } else {
            return "#";
        }

        return $link;
    }

    /**
     * Is node visible
     * @param array $a_node node data
     */
    public function isNodeVisible($a_node): bool
    {
        return $this->access->checkAccess('visible', '', $a_node["child"]);
    }

    /**
     * Sort childs
     *
     * @param array $a_childs         array of child nodes
     * @param int   $a_parent_node_id parent node id
     * @return array array of childs nodes
     */
    public function sortChilds(array $a_childs, $a_parent_node_id): array
    {
        $parent_obj_id = ilObject::_lookupObjId($a_parent_node_id);

        if ($parent_obj_id > 0) {
            $parent_type = ilObject::_lookupType($parent_obj_id);
        } else {
            $parent_type = "dummy";
            $this->type_grps["dummy"] = ["root" => "dummy"];
        }

        if (empty($this->type_grps[$parent_type])) {
            $this->type_grps[$parent_type] = $this->objDefinition->getGroupedRepositoryObjectTypes($parent_type);
        }
        $group = [];

        foreach ($a_childs as $child) {
            $g = $this->objDefinition->getGroupOfObj($child["type"]);
            if ($g == "") {
                $g = $child["type"];
            }
            $group[$g][] = $child;
        }

        // #14587 - $objDefinition->getGroupedRepositoryObjectTypes does NOT include side blocks!
        $wl = $this->getTypeWhiteList();
        if (is_array($wl) && in_array("poll", $wl)) {
            $this->type_grps[$parent_type]["poll"] = [];
        }

        $childs = [];
        foreach ($this->type_grps[$parent_type] as $t => $g) {
            if (is_array($group[$t])) {
                // do we have to sort this group??
                $sort = ilContainerSorting::_getInstance($parent_obj_id);
                $group = $sort->sortItems($group);

                // need extra session sorting here
                if ($t == "sess") {
                }

                foreach ($group[$t] as $k => $item) {
                    $childs[] = $item;
                }
            }
        }

        return $childs;
    }

    /**
     * @param int $a_parent_node_id node id
     * @return array childs array
     */
    public function getChildsOfNode($a_parent_node_id): array
    {
        if (!$this->access->checkAccess("read", "", $a_parent_node_id)) {
            return [];
        }

        return parent::getChildsOfNode($a_parent_node_id);
    }

    /**
     * Is node clickable?
     *
     * @param array $a_node node data
     * @return boolean node clickable true/false
     */
    public function isNodeClickable($a_node): bool
    {
        if ($this->select_postvar !== "") {
            // return false; #14354
        }

        if (!$this->access->checkAccess("read", "", $a_node["child"])) {
            return false;
        }
        if (!is_array($this->getClickableTypes())) {
            return true;
        }
        if (count($this->getClickableTypes()) <= 0) {
            return true;
        }
        return in_array($a_node["type"], $this->getClickableTypes());
    }

    /**
     * set an alternate highlighted node if $_GET["ref_id"] is not set or wrong
     */
    public function setHighlightedNode(int $a_value): void
    {
        $this->highlighted_node = $a_value;
    }

    /**
     * get an alternate highlighted node if $_GET["ref_id"] is not set or wrong
     * Returns null if not set
     *
     * @return mixed ref_id
     */
    public function getHighlightedNode()
    {
        return $this->highlighted_node;
    }

    /**
     * set Whitelist for clickable items
     *
     * @param array /string $a_types array type
     */
    public function setClickableTypes($a_types): void
    {
        if (!is_array($a_types)) {
            $a_types = [$a_types];
        }
        $this->clickable_types = $a_types;
    }

    /**
     * get whitelist for clickable items
     *
     * @return array types
     */
    public function getClickableTypes(): array
    {
        return (array) $this->clickable_types;
    }

    /**
     * set Whitelist for clickable items
     *
     * @param array /string $a_types array type
     */
    public function setSelectableTypes($a_types): void
    {
        if (!is_array($a_types)) {
            $a_types = [$a_types];
        }
        $this->selectable_types = $a_types;
    }

    /**
     * get whitelist for clickable items
     *
     * @return array types
     */
    public function getSelectableTypes(): array
    {
        return (array) $this->selectable_types;
    }

    /**
     * Is node selectable?
     *
     * @param mixed $a_node node object/array
     *
     * @return boolean node selectable true/false
     */
    protected function isNodeSelectable($a_node): bool
    {
        if ($this->getSelectableTypes() !== []) {
            return in_array($a_node['type'], $this->getSelectableTypes());
        }

        return true;
    }
}
