<?php

use ILIAS\DI\UIServices;
use srag\Plugins\UserDefaults\UserSearch\usrdefUser;

class usrdefUserTableGUI extends ilTable2GUI
{
    protected const TABLE_ID = 'tbl_mutla_users';
    protected const BASE_ORG_UNIT = 56;
    protected array $filter = [];
    protected ilCtrl $ctrl;
    protected UIServices $ui;
    protected ilUserDefaultsPlugin $pl;

    public function __construct(usrdefUserGUI $a_parent_obj, string $a_parent_cmd)
    {
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
        $this->setRowTemplate('tpl.row.html', $this->pl->getDirectory());
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
    protected function fillRow(array $a_set): void
    {
        foreach (array_keys($this->getSelectableColumns()) as $k) {
            if ($k === 'actions') {
                $this->tpl->setCurrentBlock('checkbox');
                $this->tpl->setVariable('ID', $a_set['usr_id']);
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
            if (in_array($field, ['repo', 'org'])) {
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

                $usrdefUser->where([$field => '%' . $value . '%'], 'LIKE');
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
        if (($this->filter['orgu'] ?? []) !== []) {
            $value = $this->filter['orgu'];
            $usrdefUser->innerjoin('il_orgu_ua', 'usr_id', 'user_id');
            $usrdefUser->where(
                ['il_orgu_ua.position_id' => ilOrgUnitPosition::CORE_POSITION_EMPLOYEE, 'il_orgu_ua.orgu_id' => $value]
            );
        }

        $this->setMaxCount($usrdefUser->count());

        $usrdefUser->where(['usr_id' => 13], '!=');
        if (!$usrdefUser->hasSets()) {
            global $DIC;
            $tpl = $DIC["tpl"];
            $tpl->setOnScreenMessage('success', 'Keine Ergebnisse für diesen Filter', true);
        }
        $usrdefUser->limit($this->getOffset(), $this->getOffset() + $this->getLimit());
        $usrdefUser->orderBy('email');

        // $usrdefUser->debug();
        $this->setData($usrdefUser->getArray());
    }

    public function getSelectableColumns(): array
    {
        $cols['firstname'] = [
            'txt' => $this->pl->txt('usr_firstname'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'firstname'
        ];
        $cols['lastname'] = [
            'txt' => $this->pl->txt('usr_lastname'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'lastname'
        ];
        $cols['email'] = [
            'txt' => $this->pl->txt('usr_email'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'email'
        ];
        $cols['login'] = [
            'txt' => $this->pl->txt('usr_login'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'login'
        ];
        $cols['actions'] = ['txt' => $this->pl->txt('common_actions'), 'default' => true, 'width' => '50px'];

        return $cols;
    }

    private function addColumns(): void
    {
        foreach ($this->getSelectableColumns() as $k => $v) {
            if ($this->isColumnSelected($k)) {
                $sort = array_key_exists('sort_field', $v) && $v['sort_field'] ? $v['sort_field'] : $k;
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
        $orgu = new ilMultiSelectInputGUI($this->pl->txt('usr_orgu'), 'orgu');
        $orgu_option = $this->buildOrgunitOptions();
        $orgu->setOptions($orgu_option);
        $this->addAndReadFilterItem($orgu);
    }

    protected function addAndReadFilterItem(ilFormPropertyGUI $item): void
    {
        $this->addFilterItem($item);
        $item->readFromSession();
        $this->filter[$item->getPostVar()] = $item->getValue();
    }

    /**
     * @return array
     */
    protected function buildOrgunitOptions(): array
    {
        $all_children = ilObjOrgUnitTree::_getInstance()->getAllChildren(self::BASE_ORG_UNIT);

        $org_units = [];
        foreach ($all_children as $child) {
            if ($child === self::BASE_ORG_UNIT) {
                continue;
            }
            $org_units[$child] = ilObject::_lookupTitle(ilObject::_lookupObjId($child));
        }

        return $org_units;
    }
}
