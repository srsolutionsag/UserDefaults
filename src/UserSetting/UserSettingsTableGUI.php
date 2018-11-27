<?php

namespace srag\Plugins\UserDefaults\UserSetting;

use ilAdvancedSelectionListGUI;
use ilExcel;
use ilLinkButton;
use ilTable2GUI;
use ilUserDefaultsPlugin;
use ilUtil;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\UserSearch\usrdefObj;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UDFCheckGUI;
use UserSettingsGUI;

/**
 * Class ilUserSettingsTableGUI
 *
 * @package srag\Plugins\UserDefaults\UserSetting
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class UserSettingsTableGUI extends ilTable2GUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const USR_DEF_CONTENT = 'usr_def_content';
	/**
	 * @var  array $filter
	 */
	protected $filter = array();
	/**
	 * @var array
	 */
	protected $ignored_cols = array();


	/**
	 * @param UserSettingsGUI $parent_obj
	 * @param string          $parent_cmd
	 * @param string          $template_context
	 */
	public function __construct(UserSettingsGUI $parent_obj, $parent_cmd = UserSettingsGUI::CMD_INDEX, $template_context = "") {
		$this->setPrefix(self::USR_DEF_CONTENT);
		$this->setFormName(self::USR_DEF_CONTENT);
		$this->setId(self::USR_DEF_CONTENT);
		$this->setTitle(self::plugin()->translate('set_table_title'));
		parent::__construct($parent_obj, $parent_cmd, $template_context);
		self::dic()->ctrl()->saveParameter($parent_obj, $this->getNavParameter());
		$this->setEnableNumInfo(true);
		$this->setFormAction(self::dic()->ctrl()->getFormAction($parent_obj));
		$this->addColumns();
		$this->setDefaultOrderField('title');
		$this->setExternalSorting(true);
		$this->setExternalSegmentation(true);
		$this->setRowTemplate('tpl.settings_row.html', self::plugin()->directory());
		$this->parseData();

		$button = ilLinkButton::getInstance();
		$button->setCaption(self::plugin()->translate("set_add"), false);
		$button->setUrl(self::dic()->ctrl()->getLinkTarget($parent_obj, UserSettingsGUI::CMD_ADD));
		$button->addCSSClass("submit");
		$button->addCSSClass("emphsubmit");
		self::dic()->toolbar()->addButtonInstance($button);

		$this->setSelectAllCheckbox('setting_select');
		$this->addMultiCommand(UserSettingsGUI::CMD_ACTIVATE_MULTIPLE_CONFIRM, self::plugin()->translate('set_activate'));
		$this->addMultiCommand(UserSettingsGUI::CMD_DEACTIVATE_MULTIPLE_CONFIRM, self::plugin()->translate('set_deactivate'));
		$this->addMultiCommand(UserSettingsGUI::CMD_DELETE_MULTIPLE_CONFIRM, self::plugin()->translate('set_delete'));
	}


	protected function parseData() {
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$xdglRequestList = UserSetting::getCollection();
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
			$a_data[$k]['status_image'] = ($d['status'] == UserSetting::STATUS_ACTIVE ? $img_on : $img_off);
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
		$ilUserSetting = UserSetting::find($a_set['id']);
		$ilUDFCheckGUI = new UDFCheckGUI($this->parent_obj);

		$this->tpl->setCurrentBlock('setting_select');
		$this->tpl->setVariable('SETTING_ID', $ilUserSetting->getId());
		$this->tpl->parseCurrentBlock();

		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($k == 'actions') {
				self::dic()->ctrl()->setParameter($this->parent_obj, UserSettingsGUI::IDENTIFIER, $ilUserSetting->getId());
				self::dic()->ctrl()->setParameter($ilUDFCheckGUI, UserSettingsGUI::IDENTIFIER, $ilUserSetting->getId());

				$current_selection_list = new ilAdvancedSelectionListGUI();
				$current_selection_list->setListTitle(self::plugin()->translate('set_actions'));
				$current_selection_list->setId('set_actions' . $ilUserSetting->getId());
				$current_selection_list->setUseImages(false);
				$current_selection_list->addItem(self::plugin()->translate('set_edit'), 'set_edit', self::dic()->ctrl()
					->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_EDIT));

				$current_selection_list->addItem(self::plugin()->translate('set_udf_checks'), 'set_udf_checks', self::dic()->ctrl()
					->getLinkTarget($ilUDFCheckGUI, UDFCheckGUI::CMD_INDEX));
				if ($ilUserSetting->getStatus() == UserSetting::STATUS_ACTIVE) {
					$current_selection_list->addItem(self::plugin()->translate('set_deactivate'), 'set_deactivate', self::dic()->ctrl()
						->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_DEACTIVATE));
				} else {
					$current_selection_list->addItem(self::plugin()->translate('set_activate'), 'set_activate', self::dic()->ctrl()
						->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_ACTIVATE));
				}
				$current_selection_list->addItem(self::plugin()->translate('set_duplicate'), 'set_duplicate', self::dic()->ctrl()
					->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_DUPLICATE));
				$current_selection_list->addItem(self::plugin()->translate('set_delete'), 'set_delete', self::dic()->ctrl()
					->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_CONFIRM_DELETE));

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
			'txt' => self::plugin()->translate('set_status'),
			'default' => true,
			'width' => '30px',
			'sort_field' => 'status',
		);
		$cols['title'] = array(
			'txt' => self::plugin()->translate('set_title'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'title',
		);
		$cols['object_data_title'] = array(
			'txt' => self::plugin()->translate('set_global_role'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'object_data_title',
		);
		$cols['on_create'] = array(
			'txt' => self::plugin()->translate('set_on_create'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['on_update'] = array(
			'txt' => self::plugin()->translate('set_on_update'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['on_manual'] = array(
			'txt' => self::plugin()->translate('set_on_manual'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['actions'] = array(
			'txt' => self::plugin()->translate('set_actions'),
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
	 * @param ilExcel $a_worksheet
	 * @param int     $a_row
	 * @param array   $a_set
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
