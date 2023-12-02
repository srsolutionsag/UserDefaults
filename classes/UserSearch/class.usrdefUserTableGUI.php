<?php

require_once __DIR__ . "/../../vendor/autoload.php";

use srag\Plugins\UserDefaults\Access\Courses;
use srag\Plugins\UserDefaults\Form\usrdefOrguSelectorInputGUI;
use srag\Plugins\UserDefaults\UserSearch\usrdefUser;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;

/**
 * @ilCtrl_Calls usrdefUserTableGUI: ilFormPropertyDispatchGUI
 */
class usrdefUserTableGUI extends ilTable2GUI {

	use UserDefaultsTrait;
	const TABLE_ID = 'tbl_mutla_users';
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	protected array $filter = [];
    protected ilCtrl $ctrl;
    private \ILIAS\DI\UIServices $ui;
    private ilUserDefaultsPlugin $pl;


    /**
     * @throws arException
     * @throws DICException
     * @throws ilCtrlException
     * @throws ilException
     */
    public function __construct(usrdefUserGUI $a_parent_obj, $a_parent_cmd) {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->ui = $DIC->ui();
        $this->pl = ilUserDefaultsPlugin::getInstance();

		$this->setId(self::TABLE_ID);
		$this->setPrefix(self::TABLE_ID);
		$this->setFormName(self::TABLE_ID);
        $this->ctrl->saveParameter($a_parent_obj, $this->getNavParameter());
		parent::__construct($a_parent_obj, $a_parent_cmd);
		$this->parent_obj = $a_parent_obj;
		$this->setRowTemplate('tpl.row.html',$this->pl->getDirectory());
		$this->setEnableNumInfo(true);
		$this->setFormAction($this->ctrl->getFormAction($a_parent_obj));
		$this->addColumns();
		$this->initFilters();
		$this->setDefaultOrderField('title');
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->setDisableFilterHiding(true);
		$this->parseData();
		$this->addCommandButton('selectUser', $this->pl->txt('button_select_user'));

		$this->setSelectAllCheckbox('id');
	}

    /**
     * @throws ilCtrlException
     */
    public function executeCommand(): bool
    {
		switch ($this->ctrl->getNextClass($this)) {
			case strtolower(__CLASS__):
			case '':
				$cmd = $this->ctrl->getCmd() . 'Cmd';
				return $this->$cmd();
			default:
                $this->ctrl->setReturn($this, 'index');
				return parent::executeCommand();
		}
	}


	public function fillRow(array $a_set): void
    {
		/**
		 * @var usrdefUser $usrdefUser
		 */
		$usrdefUser = usrdefUser::find($a_set['usr_id']);
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($k == 'actions') {
				$this->tpl->setCurrentBlock('checkbox');
				$this->tpl->setVariable('ID', $usrdefUser->getUsrId());
				$this->tpl->parseCurrentBlock();
				continue;
			}

			if ($this->isColumnSelected($k)) {
				if ($a_set[$k]) {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode(", ", $a_set[$k]) : $a_set[$k]));
					$this->tpl->parseCurrentBlock();
				} else {
					$this->tpl->setCurrentBlock('td');
					$this->tpl->setVariable('VALUE', '&nbsp;');
					$this->tpl->parseCurrentBlock();
				}
			}
		}
	}


    /**
     * @throws arException
     * @throws DICException
     * @throws Exception
     */
    protected function parseData(): void
    {
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$usrdefUser = usrdefUser::getCollection();
		$usrdefUser->orderBy($this->getOrderField(), $this->getOrderDirection());

		foreach ($this->filter as $field => $value) {
			if (in_array($field, array( 'repo', 'org' ))) {
				continue;
			}
			if ($value && !is_array($value)) {
				$value = str_replace('%', '', $value);
				if (strlen($value) < 3) {
                    global $DIC;
                    $tpl = $DIC["tpl"];
                    $tpl->setOnScreenMessage('failure', $this->pl->txt('msg_failure_more_characters_needed'), true);
					continue;
				}

				$usrdefUser->where(array( $field => '%' . $value . '%' ), 'LIKE');
			}
		}

		// CRS and GRPS
		/*if ($this->filter['repo'] && is_array($this->filter['repo'])
			&& count($this->filter['repo']) > 0) {
			$value = $this->filter['repo'];
			$obj_ids = array();
			foreach ($value as $ref_id) {
				$obj_ids[] = ilObject2::_lookupObjId((int) $ref_id);
			}

			$usrdefUser->innerjoin('obj_members', 'usr_id', 'usr_id');
			$usrdefUser->where(array(
				'obj_members.obj_id' => $obj_ids,
				'obj_members.member' => 1,
			));
		}*/

		// ORGU
		if (in_array('orgu', $this->filter) && $this->filter['orgu'] && is_array($this->filter['orgu'])
			&& count($this->filter['orgu']) > 0) {
			$value = $this->filter['orgu'];
			$role_ids = array();
			$roles = ilObjOrgUnitTree::_getInstance()->getEmployeeRoles();
			foreach ($value as $ref_id) {
				if ($roles[$ref_id]) {
					$role_ids[] = $roles[$ref_id];
				}
			}
			$usrdefUser->innerjoin('rbac_ua', 'usr_id', 'usr_id');
			$usrdefUser->where(array( 'rbac_ua.rol_id' => $role_ids ));
		}

		$this->setMaxCount($usrdefUser->count());

		$usrdefUser->where(array( 'usr_id' => 13 ), '!=');
		if (!$usrdefUser->hasSets()) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('success','Keine Ergebnisse für diesen Filter', true);
		}
		$usrdefUser->limit($this->getOffset(), $this->getOffset() + $this->getLimit());
		$usrdefUser->orderBy('email');


		// $usrdefUser->debug();
		$this->setData($usrdefUser->getArray());
	}


	public function getSelectableColumns(): array
    {
		$cols['firstname'] = array(
			'txt' => $this->pl->txt('usr_firstname'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'firstname',
		);
		$cols['lastname'] = array(
			'txt' => $this->pl->txt('usr_lastname'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'lastname',
		);
		$cols['email'] = array(
			'txt' => $this->pl->txt('usr_email'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'email',
		);
		$cols['login'] = array(
			'txt' => $this->pl->txt('usr_login'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'login',
		);
		$cols['actions'] = array(
			'txt' => $this->pl->txt('common_actions'),
			'default' => true,
			'width' => '50px',
		);

		return $cols;
	}


	private function addColumns(): void
    {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				$sort = NULL;
				if (array_key_exists('sort_field', $v) && $v['sort_field']) {
					$sort = $v['sort_field'];
				} else {
					$sort = $k;
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
	}


	protected function initFilters(): void
    {
		$this->setFilterCols(6);
		// firstname
		$te = new ilTextInputGUI($this->pl->txt('usr_firstname'), 'firstname');
		$this->addAndReadFilterItem($te);
		// lastname
		$te = new ilTextInputGUI($this->pl->txt('usr_lastname'), 'lastname');
		$this->addAndReadFilterItem($te);
		// email
		$te = new ilTextInputGUI($this->pl->txt('usr_email'), 'email');
		$this->addAndReadFilterItem($te);
		// login
		$te = new ilTextInputGUI($this->pl->txt('usr_login'), 'login');
		$this->addAndReadFilterItem($te);

		/*$crs = $this->getCrsSelectorGUI();
		$this->addAndReadFilterItem($crs);*/

		// orgu
		//todo
        //$crs = $this->getOrguSelectorGUI();
		//$this->addAndReadFilterItem($crs);

		// orgu legacy
		//		$orgu = new ilMultiSelectInputGUI($this->pl->txt('usr_orgu'), 'orgu');
		//		$orgu->setOptions(ilObjOrgUnitTree::_getInstance()->getAllChildren(56));
		//		$this->addAndReadFilterItem($orgu);
	}

	protected function addAndReadFilterItem(ilFormPropertyGUI $item): void
    {
		$this->addFilterItem($item);
		$item->readFromSession();
		$this->filter[$item->getPostVar()] = $item->getValue();
	}


	/**
	 * @param bool $a_in_determination
	 */
	/*public function resetOffset($a_in_determination = false) {
		parent::resetOffset(false);
		self::dic()->ctrl()->setParameter($this->parent_obj, $this->getNavParameter(), $this->nav_value);
	}*/


	public function getCrsSelectorGUI(): ilRepositorySelector2InputGUI
    {
		// courses
		$crs = new ilRepositorySelector2InputGUI($this->pl->txt('usr_repo'), 'repo', true);
		$crs->getExplorerGUI()->setSelectableTypes(array( 'grp', Courses::TYPE_CRS ));

		return $crs;
	}

	public function getOrguSelectorGUI(): usrdefOrguSelectorInputGUI
    {
		$crs = new usrdefOrguSelectorInputGUI($this->pl->txt('usr_orgu'), 'orgu', true);
		$crs->getExplorerGUI()->setRootId(56);
		$crs->getExplorerGUI()->setClickableTypes(array( 'orgu' ));

		return $crs;
	}
}