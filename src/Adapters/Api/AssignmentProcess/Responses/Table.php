<?php

namespace srag\Plugins\UserDefaults\Adapters\Api\AssignmentProcess\Responses;

use ILIAS\UI\Renderer;
use ILIAS\UI\Factory;
use arException;
use ilExcel;
use ilLinkButton;
use ilTable2GUI;
use ilUserDefaultsPlugin;
use ilUtil;
use srag\Plugins\UserDefaults\UserSetting\UserSetting;
use srag\Plugins\UserDefaults\Utils\UserDefaultsTrait;
use UDFCheckGUI;
use UserSettingsGUI;

class Table extends ilTable2GUI
{
    public const PLUGIN_CLASS_NAME = ilUserDefaultsPlugin::class;
    public const USR_DEF_CONTENT = 'usr_def_content';
    protected Renderer $ui_renderer;
    protected Factory $ui_factory;
    protected array $filter = [];
    protected array $ignored_cols = [];
    private ilUserDefaultsPlugin $pl;
    private \ilToolbarGUI $toolbar;

    /**
     * @throws DICException
     * @throws \ilCtrlException
     * @throws \ilException
     */
    public function __construct(
        UserSettingsGUI $parent_obj,
        string $parent_cmd = UserSettingsGUI::CMD_INDEX,
        string $template_context = ""
    ) {
        global $DIC;
        $this->ctrl = $DIC->ctrl();
        $this->pl = ilUserDefaultsPlugin::getInstance();
        $this->toolbar = $DIC->toolbar();
        $this->ui_factory = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();

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
        $button->setUrl($this->ctrl->getLinkTarget($parent_obj, UserSettingsGUI::CMD_ADD));
        $button->addCSSClass("submit");
        $button->addCSSClass("emphsubmit");

        $this->toolbar->addButtonInstance($button);

        $this->setSelectAllCheckbox('setting_select');
        $this->addMultiCommand(UserSettingsGUI::CMD_ACTIVATE_MULTIPLE_CONFIRM, $this->pl->txt('set_activate'));
        $this->addMultiCommand(UserSettingsGUI::CMD_DEACTIVATE_MULTIPLE_CONFIRM, $this->pl->txt('set_deactivate'));
        $this->addMultiCommand(UserSettingsGUI::CMD_DELETE_MULTIPLE_CONFIRM, $this->pl->txt('set_delete'));
    }

    /**
     * @throws \ilException
     * @throws \ilCtrlException
     */
    public static function new(object $parentIliasGui): self
    {
        return new self($parentIliasGui);
    }

    /**
     * @throws arException
     */
    protected function parseData(): void
    {
        $this->determineOffsetAndOrder();
        $this->determineLimit();
        $xdglRequestList = UserSetting::getCollection();
        $xdglRequestList->orderBy($this->getOrderField(), $this->getOrderDirection());

        foreach ($this->filter as $field => $value) {
            if ($value) {
                $xdglRequestList->where([$field => $value]);
            }
        }
        $this->setMaxCount($xdglRequestList->count());
        if (!$xdglRequestList->hasSets()) {
            //			ilUtil::sendInfo('Keine Ergebnisse für diesen Filter');
        }
        $xdglRequestList->limit($this->getOffset(), $this->getOffset() + $this->getLimit());
        $xdglRequestList->orderBy('title');
        $a_data = $xdglRequestList->getArray();

        $img_on = ilUtil::img(ilUtil::getImagePath('standard/icon_ok.svg'), null, "20px", "20px");
        $img_off = ilUtil::img(ilUtil::getImagePath('standard/icon_not_ok.svg'), null, "20px", "20px");

        foreach ($a_data as $k => $d) {
            $a_data[$k]['status_image'] = ($d['status'] == UserSetting::STATUS_ACTIVE ? $img_on : $img_off);
            $a_data[$k]['on_create'] = ($d['on_create'] ? $img_on : $img_off);
            $a_data[$k]['on_update'] = ($d['on_update'] ? $img_on : $img_off);
            $a_data[$k]['on_manual'] = ($d['on_manual'] ? $img_on : $img_off);
        }
        $this->setData($a_data);
    }

    /**
     * @throws \ilTemplateException
     * @throws \ilCtrlException
     * @throws DICException
     * @throws \JsonException
     */
    #[\Override]
    protected function fillRow(array $a_set): void
    {
        $user_settings = UserSetting::find($a_set['id']) ?? throw new \ilException('User Setting not found: ' . $a_set['id']);

        $this->tpl->setCurrentBlock('setting_select');
        $this->tpl->setVariable('SETTING_ID', $user_settings->getId());
        $this->tpl->parseCurrentBlock();

        foreach (array_keys($this->getSelectableColumns()) as $k) {
            if ($k == 'actions') {
                $this->ctrl->setParameter($this->parent_obj, UserSettingsGUI::IDENTIFIER, $user_settings->getId());
                $this->ctrl->setParameterByClass(UDFCheckGUI::class, UserSettingsGUI::IDENTIFIER, $user_settings->getId());

                // Actions
                // Create selection list for actions
                $actions = [
                    'set_edit' => $this->ui_factory->button()->shy(
                        $this->pl->txt('check_edit'),
                        $this->ctrl->getLinkTargetByClass(UserSettingsGUI::class, UserSettingsGUI::CMD_EDIT)
                    ),
                    'set_udf_checks' => $this->ui_factory->button()->shy(
                        $this->pl->txt('set_udf_checks'),
                        $this->ctrl->getLinkTargetByClass(UDFCheckGUI::class, UDFCheckGUI::CMD_INDEX)
                    ),
                ];

                if ($user_settings->getStatus() === UserSetting::STATUS_ACTIVE) {
                    $actions['set_deactivate'] = $this->ui_factory->button()->shy(
                        $this->pl->txt('set_deactivate'),
                        $this->ctrl->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_DEACTIVATE)
                    );
                } else {
                    $actions['set_activate'] = $this->ui_factory->button()->shy(
                        $this->pl->txt('set_activate'),
                        $this->ctrl->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_ACTIVATE)
                    );
                }

                $actions['set_duplicate'] = $this->ui_factory->button()->shy(
                    $this->pl->txt('set_duplicate'),
                    $this->ctrl->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_DUPLICATE)
                );
                $actions['set_delete'] = $this->ui_factory->button()->shy(
                    $this->pl->txt('set_delete'),
                    $this->ctrl->getLinkTarget($this->parent_obj, UserSettingsGUI::CMD_CONFIRM_DELETE)
                );

                $action_list = $this->ui_factory->dropdown()->standard(
                    $actions
                );

                $this->tpl->setCurrentBlock('td');
                $this->tpl->setVariable('VALUE', $this->ui_renderer->render($action_list));
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

    protected function setTableHeaders()
    {
    }

    public function initFilter(): void
    {
        //we don't want a filter here. So we override this method.
    }

    #[\Override]
    public function getSelectableColumns(): array
    {
        return ['status_image' => [
            'txt' => $this->pl->txt('set_status'),
            'default' => true,
            'width' => '30px',
            'sort_field' => 'status'
        ], 'title' => [
            'txt' => $this->pl->txt('set_title'),
            'default' => true,
            'width' => 'auto',
            'sort_field' => 'title'
        ], 'on_create' => ['txt' => $this->pl->txt('set_on_create'), 'default' => true, 'width' => 'auto'], 'on_update' => ['txt' => $this->pl->txt('set_on_update'), 'default' => true, 'width' => 'auto'], 'on_manual' => ['txt' => $this->pl->txt('set_on_manual'), 'default' => true, 'width' => 'auto'], 'actions' => ['txt' => $this->pl->txt('set_actions'), 'default' => true, 'width' => '150px']];
    }

    private function addColumns(): void
    {
        $this->addColumn('');

        foreach ($this->getSelectableColumns() as $k => $v) {
            if ($this->isColumnSelected($k)) {
                $sort = array_key_exists('sort_field', $v) && $v['sort_field'] ? $v['sort_field'] : false;
                $this->addColumn($v['txt'], $sort, $v['width']);
            }
        }
    }

    #[\Override]
    public function setExportFormats(array $formats): void
    {
        parent::setExportFormats([self::EXPORT_EXCEL, self::EXPORT_CSV]);
    }

    #[\Override]
    protected function fillRowExcel(ilExcel $a_worksheet, int &$a_row, array $a_set): void
    {
        $col = 0;
        foreach ($a_set as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (!in_array($key, $this->getIgnoredCols()) && $this->isColumnSelected($key)) {
                $a_worksheet->setCell($a_row, $col, strip_tags((string) $value));
                $col++;
            }
        }
    }

    #[\Override]
    protected function fillRowCSV(object $a_csv, array $a_set): void
    {
        foreach ($a_set as $key => $value) {
            if (is_array($value)) {
                $value = implode(', ', $value);
            }
            if (in_array($key, $this->getIgnoredCols())) {
                continue;
            }
            if (!$this->isColumnSelected($key)) {
                continue;
            }
            $a_csv->addColumn(strip_tags((string) $value));
        }
        $a_csv->addRow();
    }

    #[\Override]
    public function numericOrdering($sort_field): bool
    {
        return in_array($sort_field, []);
    }

    public function setIgnoredCols(array $ignored_cols): void
    {
        $this->ignored_cols = $ignored_cols;
    }

    public function getIgnoredCols(): array
    {
        return $this->ignored_cols;
    }
}
