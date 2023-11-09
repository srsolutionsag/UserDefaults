<?php

namespace srag\Plugins\UserDefaults\UDFCheck;

use ilAdvancedSelectionListGUI;
use ilExcel;
use ILIAS\UI\Component\Image\Factory;
use ILIAS\UI\Renderer;
use ilLinkButton;
use ilTable2GUI;
use ilUserDefaultsPlugin;
use ilUtil;
use srag\DIC\UserDefaults\DICTrait;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UDFCheckGUI;
use UserSettingsGUI;

/**
 * Class UDFCheckTableGUI
 *
 * @package srag\Plugins\UserDefaults\UDFCheck
 *
 * @author  Fabian Schmid <fs@studer-raimann.ch>
 * @version 1.0.0
 */
class UDFCheckTableGUI extends ilTable2GUI {

	use DICTrait;
	use UserDefaultsTrait;
	const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
	const USR_DEF_CONTENT = 'usr_def_content_checks';
	protected array $filter = array();
	protected array $ignored_cols = array();
	protected Renderer $renderer;
	protected Factory $image;

	public function __construct(UDFCheckGUI $parent_obj, string $parent_cmd = UDFCheckGUI::CMD_INDEX, string $template_context = "") {
		$this->renderer = self::dic()->ui()->renderer();
		$this->image = self::dic()->ui()->factory()->image();

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

	protected function parseData(): void
    {
		$this->determineOffsetAndOrder();
		$this->determineLimit();

		$checks = UDFCheck::getChecksByParent(filter_input(INPUT_GET, UserSettingsGUI::IDENTIFIER), true, $this->filter, [
			$this->getOffset(),
			$this->getOffset() + $this->getLimit()
		]);

		$this->setMaxCount(count($checks));

		/*if (count($checks) === 0) {
			ilUtil::sendInfo('Keine Ergebnisse für diesen Filter'); // TODO: Translate
		}*/

		$this->setData($checks);
	}

	public function fillRow(array $a_set): void
    {
		$a_set["operator"] = self::plugin()->translate("check_op_" . UDFCheck::$operator_text_keys[$a_set["operator"]]);

		$ilUDFCheckGUI = new UDFCheckGUI($this->parent_obj);
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($k == 'actions') {
				self::dic()->ctrl()->setParameter($this->parent_obj, UDFCheckGUI::IDENTIFIER_CATEGORY, $a_set["field_category"]);
				self::dic()->ctrl()->setParameter($ilUDFCheckGUI, UDFCheckGUI::IDENTIFIER_CATEGORY, $a_set["field_category"]);
				self::dic()->ctrl()->setParameter($this->parent_obj, UDFCheckGUI::IDENTIFIER, $a_set["id"]);
				self::dic()->ctrl()->setParameter($ilUDFCheckGUI, UDFCheckGUI::IDENTIFIER, $a_set["id"]);

				$current_selection_list = new ilAdvancedSelectionListGUI();
				$current_selection_list->setListTitle(self::plugin()->translate('check_actions'));
				$current_selection_list->setId('check_actions' . $a_set["id"]);
				$current_selection_list->setUseImages(false);
				$current_selection_list->addItem(self::plugin()->translate('check_edit'), 'check_edit', self::dic()->ctrl()
					->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_EDIT));
				$current_selection_list->addItem(self::plugin()->translate('check_delete'), 'check_delete', self::dic()->ctrl()
					->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_CONFIRM_DELETE));

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
							$r = self::output()->getHTML($this->image->standard(ilUtil::getImagePath('icon_checked.svg'), 'negated'));
							$this->tpl->setVariable('VALUE', $r);
						} else {
							$this->tpl->setVariable('VALUE', '&nbsp;');
						}
						$this->tpl->parseCurrentBlock();
						break;
					case "field_key":
						$this->tpl->setCurrentBlock('td');
						if ($a_set["field_key_txt"]) {
							$this->tpl->setVariable('VALUE', $a_set["field_key_txt"]);
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


	public function initFilter(): void
    {
		//we don't want a filter here. So we override this method.
	}

	public function getSelectableColumns(): array
    {
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


	private function addColumns(): void
    {
		foreach ($this->getSelectableColumns() as $k => $v) {
			if ($this->isColumnSelected($k)) {
				if (array_key_exists('sort_field', $v) && $v['sort_field']) {
					$sort = $v['sort_field'];
				} else {
					$sort = $k;
				}
				$this->addColumn($v['txt'], $sort, $v['width']);
			}
		}
	}

	public function setExportFormats(array $formats): void
    {
		parent::setExportFormats(array( self::EXPORT_EXCEL, self::EXPORT_CSV ));
	}

	protected function fillRowExcel(ilExcel $a_worksheet, int &$a_row, array $a_set): void
    {
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

	protected function fillRowCSV(\ilCSVWriter $a_csv, array $a_set): void
    {
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


	public function numericOrdering(string $sort_field): bool
    {
		return in_array($sort_field, array());
	}

	public function setIgnoredCols($ignored_cols): void
    {
		$this->ignored_cols = $ignored_cols;
	}

	public function getIgnoredCols(): array
    {
		return $this->ignored_cols;
	}
}
