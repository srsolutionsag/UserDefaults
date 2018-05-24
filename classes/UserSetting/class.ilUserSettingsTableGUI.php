<?php

/**
 * Class ilUserSettingsTableGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUserSettingsTableGUI extends ilTable2GUI {

	const USR_DEF_CONTENT = 'usr_def_content';
	/**
	 * @var ilCtrl $ctrl
	 */
	protected $ctrl;
	/**
	 * @var  array $filter
	 */
	protected $filter = array();
	/**
	 * @var array
	 */
	protected $ignored_cols = array();
	/**
	 * @var ilUserDefaultsPlugin
	 */
	protected $pl;
	/**
	 * @var ilToolbarGUI
	 */
	protected $toolbar;


	/**
	 * @param ilUserSettingsGUI $parent_obj
	 * @param string            $parent_cmd
	 * @param string            $template_context
	 */
	public function __construct(ilUserSettingsGUI $parent_obj, $parent_cmd = ilUserSettingsGUI::CMD_INDEX, $template_context = "") {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->pl = ilUserDefaultsPlugin::getInstance();
		$this->toolbar = $DIC->toolbar();

		$this->setPrefix(self::USR_DEF_CONTENT);
		$this->setFormName(self::USR_DEF_CONTENT);
		$this->setId(self::USR_DEF_CONTENT);
		$this->setTitle($this->pl->txt('set_table_title'));
		parent::__construct($parent_obj, $parent_cmd, $template_context);
		$this->ctrl->saveParameter($parent_obj, $this->getNavParameter());
		$this->setEnableNumInfo(true);
		$this->setFormAction($this->ctrl->getFormAction($parent_obj));
		$this->addColumns();
		$this->setDefaultOrderField('title');
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->setRowTemplate('tpl.settings_row.html', $this->pl->getDirectory());
		$this->parseData();

		$button = ilLinkButton::getInstance();
		$button->setCaption($this->pl->txt("set_add"), false);
		$button->setUrl($this->ctrl->getLinkTarget($parent_obj, ilUserSettingsGUI::CMD_ADD));
		$button->addCSSClass("submit");
		$button->addCSSClass("emphsubmit");
		$this->toolbar->addButtonInstance($button);

		$this->setSelectAllCheckbox('setting_select');
		$this->addMultiCommand(ilUserSettingsGUI::CMD_ACTIVATE_MULTIPLE_CONFIRM, $this->pl->txt('set_activate'));
		$this->addMultiCommand(ilUserSettingsGUI::CMD_DEACTIVATE_MULTIPLE_CONFIRM, $this->pl->txt('set_deactivate'));
		$this->addMultiCommand(ilUserSettingsGUI::CMD_DELETE_MULTIPLE_CONFIRM, $this->pl->txt('set_delete'));
	}


	protected function parseData() {
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$xdglRequestList = ilUserSetting::getCollection();
		$xdglRequestList->orderBy($this->getOrderField(), $this->getOrderDirection());
		$xdglRequestList->leftjoin(usrdefObj::TABLE_NAME, 'global_role', 'obj_id', array( 'title' ));

		foreach ($this->filter as $field => $value) {
			if ($value) {
				$xdglRequestList->where(array( $field => $value ));
			}
		}
		$this->setMaxCount($xdglRequestList->count());
		if (!$xdglRequestList->hasSets()) {
			//			ilUtil::sendInfo('Keine Ergebnisse für diesen Filter');
		}
		$xdglRequestList->limit($this->getOffset(), $this->getOffset() + $this->getLimit());
		$xdglRequestList->orderBy('title');
		$a_data = $xdglRequestList->getArray();

		$img_on = ilUtil::img(ilUtil::getImagePath('icon_ok.svg'));
		$img_off = ilUtil::img(ilUtil::getImagePath('icon_not_ok.svg'));

		foreach ($a_data as $k => $d) {
			$a_data[$k]['status_image'] = ($d['status'] == ilUserSetting::STATUS_ACTIVE ? $img_on : $img_off);
			$a_data[$k]['on_create'] = ($d['on_create'] ? $img_on : $img_off);
			$a_data[$k]['on_update'] = ($d['on_update'] ? $img_on : $img_off);
			$a_data[$k]['on_manual'] = ($d['on_manual'] ? $img_on : $img_off);
		}
		$this->setData($a_data);
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$ilUserSetting = ilUserSetting::find($a_set['id']);
		$ilUDFCheckGUI = new ilUDFCheckGUI($this->parent_obj);

		$this->tpl->setCurrentBlock('setting_select');
		$this->tpl->setVariable('SETTING_ID', $ilUserSetting->getId());
		$this->tpl->parseCurrentBlock();

		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($k == 'actions') {
				$this->ctrl->setParameter($this->parent_obj, ilUserSettingsGUI::IDENTIFIER, $ilUserSetting->getId());
				$this->ctrl->setParameter($ilUDFCheckGUI, ilUserSettingsGUI::IDENTIFIER, $ilUserSetting->getId());

				$current_selection_list = new ilAdvancedSelectionListGUI();
				$current_selection_list->setListTitle($this->pl->txt('set_actions'));
				$current_selection_list->setId('set_actions' . $ilUserSetting->getId());
				$current_selection_list->setUseImages(false);
				$current_selection_list->addItem($this->pl->txt('set_edit'), 'set_edit', $this->ctrl->getLinkTarget($this->parent_obj, ilUserSettingsGUI::CMD_EDIT));

				$current_selection_list->addItem($this->pl->txt('set_udf_checks'), 'set_udf_checks', $this->ctrl->getLinkTarget($ilUDFCheckGUI, ilUDFCheckGUI::CMD_INDEX));
				if ($ilUserSetting->getStatus() == ilUserSetting::STATUS_ACTIVE) {
					$current_selection_list->addItem($this->pl->txt('set_deactivate'), 'set_deactivate', $this->ctrl->getLinkTarget($this->parent_obj, ilUserSettingsGUI::CMD_DEACTIVATE));
				} else {
					$current_selection_list->addItem($this->pl->txt('set_activate'), 'set_activate', $this->ctrl->getLinkTarget($this->parent_obj, ilUserSettingsGUI::CMD_ACTIVATE));
				}
				$current_selection_list->addItem($this->pl->txt('set_duplicate'), 'set_duplicate', $this->ctrl->getLinkTarget($this->parent_obj, ilUserSettingsGUI::CMD_DUPLICATE));
				$current_selection_list->addItem($this->pl->txt('set_delete'), 'set_delete', $this->ctrl->getLinkTarget($this->parent_obj, ilUserSettingsGUI::CMD_CONFIRM_DELETE));

				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', $current_selection_list->getHTML());
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


	protected function setTableHeaders() {
	}


	public function initFilter() {
		//we don't want a filter here. So we override this method.
	}


	/**
	 * @return array
	 */
	public function getSelectableColumns() {
		$cols['status_image'] = array(
			'txt' => $this->pl->txt('set_status'),
			'default' => true,
			'width' => '30px',
			'sort_field' => 'status',
		);
		$cols['title'] = array(
			'txt' => $this->pl->txt('set_title'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'title',
		);
		$cols['object_data_title'] = array(
			'txt' => $this->pl->txt('set_global_role'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'object_data_title',
		);
		$cols['on_create'] = array(
			'txt' => $this->pl->txt('set_on_create'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['on_update'] = array(
			'txt' => $this->pl->txt('set_on_update'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['on_manual'] = array(
			'txt' => $this->pl->txt('set_on_manual'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['actions'] = array(
			'txt' => $this->pl->txt('set_actions'),
			'default' => true,
			'width' => '150px',
		);

		return $cols;
	}


	private function addColumns() {
		$this->addColumn('');

		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($v['sort_field']) {
					$sort = $v['sort_field'];
				} else {
					$sort = false;
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
	}


	/**
	 * @param array $formats
	 */
	public function setExportFormats(array $formats) {
		parent::setExportFormats(array( self::EXPORT_EXCEL, self::EXPORT_CSV ));
	}


	/**
	 * @param \ilExcel $a_worksheet
	 * @param int      $a_row
	 * @param array    $a_set
	 */
	protected function fillRowExcel(ilExcel $a_worksheet, &$a_row, $a_set) {
		$col = 0;
		foreach ($a_set as $key => $value) {
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			if (!in_array($key, $this->getIgnoredCols()) AND $this->isColumnSelected($key)) {
				$a_worksheet->writeString($a_row, $col, strip_tags($value));
				$col ++;
			}
		}
	}


	/**
	 * @param object $a_csv
	 * @param array  $a_set
	 */
	protected function fillRowCSV($a_csv, $a_set) {
		foreach ($a_set as $key => $value) {
			if (is_array($value)) {
				$value = implode(', ', $value);
			}
			if (!in_array($key, $this->getIgnoredCols()) AND $this->isColumnSelected($key)) {
				$a_csv->addColumn(strip_tags($value));
			}
		}
		$a_csv->addRow();
	}


	/**
	 * @param $sort_field
	 *
	 * @return bool
	 */
	public function numericOrdering($sort_field) {
		return in_array($sort_field, array());
	}


	/**
	 * @param array $ignored_cols
	 */
	public function setIgnoredCols($ignored_cols) {
		$this->ignored_cols = $ignored_cols;
	}


	/**
	 * @return array
	 */
	public function getIgnoredCols() {
		return $this->ignored_cols;
	}
}
