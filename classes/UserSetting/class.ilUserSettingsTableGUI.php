<?php
require_once('./Services/Table/classes/class.ilTable2GUI.php');
require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/UserSetting/class.ilUserSetting.php');
require_once('./Services/UIComponent/AdvancedSelectionList/classes/class.ilAdvancedSelectionListGUI.php');
require_once('./Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/classes/UDFCheck/class.ilUDFCheckGUI.php');

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
	 * @param ilUserSettingsGUI $parent_obj
	 * @param string $parent_cmd
	 * @param string $template_context
	 */
	public function __construct(ilUserSettingsGUI $parent_obj, $parent_cmd = ilUserSettingsGUI::CMD_INDEX, $template_context = "") {
		/**
		 * @var              $ilCtrl ilCtrl
		 * @var ilToolbarGUI $ilToolbar
		 */
		global $ilCtrl, $ilToolbar;

		$this->ctrl = $ilCtrl;
		$this->pl = ilUserDefaultsPlugin::getInstance();
		$this->toolbar = $ilToolbar;

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
		$this->setRowTemplate('Customizing/global/plugins/Services/EventHandling/EventHook/UserDefaults/templates/default/tpl.settings_row.html');
		$this->parseData();

		$this->toolbar->addButton($this->pl->txt('set_add'), $this->ctrl->getLinkTarget($parent_obj, ilUserSettingsGUI::CMD_ADD), '', '', '', '', 'submit emphsubmit');
	}


	protected function parseData() {
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$xdglRequestList = ilUserSetting::getCollection();
		$xdglRequestList->orderBy($this->getOrderField(), $this->getOrderDirection());
		$xdglRequestList->innerjoin('object_data', 'global_role', 'obj_id', array( 'title' ));

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
		if (ilUserDefaultsPlugin::is50()) {
			$img_on = ilUtil::img(ilUtil::getImagePath('icon_ok.svg'));
			$img_off = ilUtil::img(ilUtil::getImagePath('icon_not_ok.svg'));
		} else {
			$img_on = ilUtil::img(ilUtil::getImagePath('icon_led_on_s.png'));
			$img_off = ilUtil::img(ilUtil::getImagePath('icon_led_off_s.png'));
		}

		foreach ($a_data as $k => $d) {
			$a_data[$k]['status_image'] = ($d['status'] == ilUserSetting::STATUS_ACTIVE ? $img_on : $img_off);
		}
		$this->setData($a_data);
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$ilUserSetting = ilUserSetting::find($a_set['id']);
		$ilUDFCheckGUI = new ilUDFCheckGUI($this->parent_obj);
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
		$cols['status_image'] = array( 'txt' => $this->pl->txt('set_status'), 'default' => true, 'width' => '30px', 'sort_field' => 'status' );
		$cols['title'] = array( 'txt' => $this->pl->txt('set_title'), 'default' => true, 'width' => 'auto', 'sort_field' => 'title' );
		$cols['object_data_title'] = array(
			'txt'        => $this->pl->txt('set_global_role'),
			'default'    => true,
			'width'      => 'auto',
			'sort_field' => 'object_data_title',
		);
		$cols['actions'] = array( 'txt' => $this->pl->txt('set_actions'), 'default' => true, 'width' => '150px', );

		return $cols;
	}


	private function addColumns() {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if ($v['sort_field']) {
					$sort = $v['sort_field'];
				} else {
					$sort = $k;
				}
				$this->addColumn($v['txt'], ($k == 'actions' ? false : $sort), $v['width']);
			}
		}
	}


	public function setExportFormats(array $formats) {
		parent::setExportFormats(array( self::EXPORT_EXCEL, self::EXPORT_CSV ));
	}


	/**
	 * @param \ilExcel $a_worksheet
	 * @param int $a_row
	 * @param array $a_set
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
	 * @param array $a_set
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
