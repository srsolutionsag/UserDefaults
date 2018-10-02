<?php

namespace srag\Plugins\UserDefaults\UDFCheck;
use ilAdvancedSelectionListGUI;
use ilExcel;
use ILIAS\UI\Component\Image\Factory;
use ILIAS\UI\Renderer;
use ilLinkButton;
use ilTable2GUI;
use UDFCheckGUI;
use ilUserDefaultsPlugin;
use UserSettingsGUI;
use ilUtil;
use srag\DIC\DICTrait;

/**
 * Class UDFCheckTableGUI
 *
 * @package srag\Plugins\UserDefaults\UDFChec
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class UDFCheckTableGUI extends ilTable2GUI {

	use DICTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const USR_DEF_CONTENT = 'usr_def_content_checks';
	/**
	 * @var  array $filter
	 */
	protected $filter = array();
	/**
	 * @var array
	 */
	protected $ignored_cols = array();
	/**
	 * @var Renderer
	 */
	protected $renderer;
	/**
	 * @var Factory
	 */
	protected $image;


	/**
	 * @param UDFCheckGUI $parent_obj
	 * @param string      $parent_cmd
	 * @param string      $template_context
	 */
	public function __construct(UDFCheckGUI $parent_obj, $parent_cmd = UDFCheckGUI::CMD_INDEX, $template_context = "") {
		$this->renderer = self::dic()->ui()->renderer();
		$this->image =  self::dic()->ui()->factory()->image();

		$this->setPrefix(self::USR_DEF_CONTENT);
		$this->setFormName(self::USR_DEF_CONTENT);
		$this->setId(self::USR_DEF_CONTENT);
		$this->setTitle(self::plugin()->translate('check_table_title'));
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
		$button->setCaption(self::plugin()->translate("check_back"), false);
		$button->setUrl(self::dic()->ctrl()->getLinkTargetByClass(UserSettingsGUI::class, UserSettingsGUI::CMD_INDEX));
		self::dic()->toolbar()->addButtonInstance($button);

		$button = ilLinkButton::getInstance();
		$button->setCaption(self::plugin()->translate("check_add"), false);
		$button->setUrl(self::dic()->ctrl()->getLinkTarget($parent_obj, UDFCheckGUI::CMD_ADD));
		$button->addCSSClass("submit");
		$button->addCSSClass("emphsubmit");
		self::dic()->toolbar()->addButtonInstance($button);
	}


	protected function parseData() {
		$this->determineOffsetAndOrder();
		$this->determineLimit();
		$xdglRequestList = UDFCheck::getCollection();
		$xdglRequestList->where(array( 'parent_id' => $_GET[UserSettingsGUI::IDENTIFIER] ));

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
		$a_set["operator"] = self::plugin()->translate("check_op_" . UDFCheck::$operator_text_keys[$a_set["operator"]]);

		$ilUDFCheckGUI = new UDFCheckGUI($this->parent_obj);
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($k == 'actions') {
				self::dic()->ctrl()->setParameter($this->parent_obj, UDFCheckGUI::IDENTIFIER, $a_set["id"]);
				self::dic()->ctrl()->setParameter($ilUDFCheckGUI, UDFCheckGUI::IDENTIFIER, $a_set["id"]);

				$current_selection_list = new ilAdvancedSelectionListGUI();
				$current_selection_list->setListTitle(self::plugin()->translate('check_actions'));
				$current_selection_list->setId('check_actions' . $a_set["id"]);
				$current_selection_list->setUseImages(false);
				$current_selection_list->addItem(self::plugin()->translate('check_edit'), 'check_edit', self::dic()->ctrl()->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_EDIT));
				$current_selection_list->addItem(self::plugin()->translate('check_delete'), 'check_delete', self::dic()->ctrl()->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_CONFIRM_DELETE));

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
					case "field_key":
						$this->tpl->setCurrentBlock('td');
						$this->tpl->setVariable('VALUE', UDFCheck::getDefinitionFieldTitleForKey($a_set[$k]));
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
		$cols['field_key'] = array(
			'txt' => self::plugin()->translate('check_name'),
			'default' => true,
			'width' => '40%',
			'sort_field' => 'udf_definition_field_name',
		);
		$cols['check_value'] = array(
			'txt' => self::plugin()->translate('check_value'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'check_value',
		);
		$cols['negated'] = array(
			'txt' => self::plugin()->translate('check_negation_gobal'),
			'default' => true,
			'width' => 'auto',
			'sort_field' => 'check_negated',
		);
		$cols['operator'] = array(
			'txt' => self::plugin()->translate('check_operator'),
			'default' => true,
			'width' => 'auto',
		);
		$cols['actions'] = array(
			'txt' => self::plugin()->translate('check_actions'),
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
	 * @param ilExcel $a_worksheet
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
