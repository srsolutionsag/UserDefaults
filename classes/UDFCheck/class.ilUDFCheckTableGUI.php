<?php

/**
 * Class ilUDFCheckTableGUI
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class ilUDFCheckTableGUI extends ilTable2GUI {

	const USR_DEF_CONTENT = 'usr_def_content_checks';
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
	 * @var \ILIAS\UI\Renderer
	 */
	protected $renderer;
	/**
	 * @var \ILIAS\UI\Component\Image\Factory
	 */
	protected $image;


	/**
	 * @param ilUDFCheckGUI $parent_obj
	 * @param string        $parent_cmd
	 * @param string        $template_context
	 */
	public function __construct(ilUDFCheckGUI $parent_obj, $parent_cmd = ilUDFCheckGUI::CMD_INDEX, $template_context = "") {
		global $DIC;

		$this->ctrl = $DIC->ctrl();
		$this->pl = ilUserDefaultsPlugin::getInstance();
		$this->toolbar = $DIC->toolbar();
		$this->renderer = $DIC->ui()->renderer();
		$this->image = $DIC->ui()->factory()->image();

		$this->setPrefix(self::USR_DEF_CONTENT);
		$this->setFormName(self::USR_DEF_CONTENT);
		$this->setId(self::USR_DEF_CONTENT);
		$this->setTitle($this->pl->txt('check_table_title'));
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
		$button->setCaption($this->pl->txt("check_back"), false);
		$button->setUrl($this->ctrl->getLinkTargetByClass(ilUserSettingsGUI::class, ilUserSettingsGUI::CMD_INDEX));
		$this->toolbar->addButtonInstance($button);

		$button = ilLinkButton::getInstance();
		$button->setCaption($this->pl->txt("check_add"), false);
		$button->setUrl($this->ctrl->getLinkTarget($parent_obj, ilUDFCheckGUI::CMD_ADD));
		$button->addCSSClass("submit");
		$button->addCSSClass("emphsubmit");
		$this->toolbar->addButtonInstance($button);
	}


	protected function parseData() {
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$xdglRequestList = ilUDFCheck::getCollection();
		$xdglRequestList->where(array( 'parent_id' => $_GET[ilUserSettingsGUI::IDENTIFIER] ));
		$xdglRequestList->innerjoin('udf_definition', 'udf_field_id', 'field_id', array( 'field_name' ));

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
		$a_data = $xdglRequestList->getArray();

		$this->setData($a_data);
	}


	/**
	 * @param array $a_set
	 */
	public function fillRow($a_set) {
		$a_set["operator"] = $this->pl->txt("check_op_" . ilUDFCheck::$operator_text_keys[$a_set["operator"]]);

		$ilUDFCheckGUI = new ilUDFCheckGUI($this->parent_obj);
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($k == 'actions') {
				$this->ctrl->setParameter($this->parent_obj, ilUDFCheckGUI::IDENTIFIER, $a_set["id"]);
				$this->ctrl->setParameter($ilUDFCheckGUI, ilUDFCheckGUI::IDENTIFIER, $a_set["id"]);

				$current_selection_list = new ilAdvancedSelectionListGUI();
				$current_selection_list->setListTitle($this->pl->txt('check_actions'));
				$current_selection_list->setId('check_actions' . $a_set["id"]);
				$current_selection_list->setUseImages(false);
				$current_selection_list->addItem($this->pl->txt('check_edit'), 'check_edit', $this->ctrl->getLinkTarget($this->parent_obj, ilUserSettingsGUI::CMD_EDIT));
				$current_selection_list->addItem($this->pl->txt('check_delete'), 'check_delete', $this->ctrl->getLinkTarget($this->parent_obj, ilUserSettingsGUI::CMD_CONFIRM_DELETE));

				$this->tpl->setCurrentBlock('td');
				$this->tpl->setVariable('VALUE', $current_selection_list->getHTML());
				$this->tpl->parseCurrentBlock();
				continue;
			}

			if ($this->isColumnSelected($k)) {
				switch ($k) {
					case "negated":
						$this->tpl->setCurrentBlock('td');
						if ($a_set[$k]) {
							$r = $this->renderer->render($this->image->standard(ilUtil::getImagePath('icon_checked.svg'), 'negated'));
							$this->tpl->setVariable('VALUE', $r);
						} else {
							$this->tpl->setVariable('VALUE', '&nbsp;');
						}
						$this->tpl->parseCurrentBlock();
						break;
					default:
						if ($a_set[$k]) {
							$this->tpl->setCurrentBlock('td');
							$this->tpl->setVariable('VALUE', (is_array($a_set[$k]) ? implode(", ", $a_set[$k]) : $a_set[$k]));
							$this->tpl->parseCurrentBlock();
						} else {
							$this->tpl->setCurrentBlock('td');
							$this->tpl->setVariable('VALUE', '&nbsp;');
							$this->tpl->parseCurrentBlock();
						}
						break;
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
		$cols['udf_definition_field_name'] = array(
			'txt' => $this->pl->txt('check_name'),
			'default' => true,
			'width' => '40%',
			'sort_field' => 'udf_definition_field_name',
		);
		$cols['check_value'] = array(
			'txt' => $this->pl->txt('check_value'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'check_value',
		);
		$cols['negated'] = array(
			'txt' => $this->pl->txt('check_negation_gobal'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'check_negated',
		);
		$cols['operator'] = array(
			'txt' => $this->pl->txt('check_operator'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['actions'] = array(
			'txt' => $this->pl->txt('check_actions'),
			'default' => true,
			'width' => '150px',
		);

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
