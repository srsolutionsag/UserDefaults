<?php

use ILIAS\Data\Factory as DataFactory;
use ILIAS\Data\Order;
use ILIAS\Data\Range;
use ILIAS\UI\Component\Input\Container\Filter\Standard as StandardFilter;
use ILIAS\UI\Component\Table\Data as DataTable;
use ILIAS\UI\Component\Table\DataRetrieval;
use ILIAS\UI\Component\Table\DataRowBuilder;
use ILIAS\UI\Factory as UIFactory;
use ILIAS\UI\Renderer as UIRenderer;
use ILIAS\UI\URLBuilder;
use ILIAS\UI\URLBuilderToken;
use Psr\Http\Message\ServerRequestInterface;

class usrdefUserTableGUI implements DataRetrieval
{
    public const ACTION_ASSIGN = 'assign';
    public const ALL_OBJECTS = 'ALL_OBJECTS';
    public const NAMESPACE = ['usrdef'];
    public const TOKEN_ACTION = 'action';
    public const TOKEN_ROW_ID = 'ids';

    protected const TABLE_ID = 'usrdef_users';
    protected const FILTER_ID = 'usrdef_user_filter';

    /**
     * Order fields are handed to ilUserQuery unvalidated, so only accept the
     * ones this table actually offers.
     */
    protected const SORTABLE = ['login', 'firstname', 'lastname', 'email'];

    /**
     * DataQuery::withLimitedUsers() treats an empty list as "no restriction".
     * An org unit matching nobody therefore has to be expressed as an id that
     * cannot exist, or the filter would widen the result to every user.
     */
    protected const MATCHES_NOBODY = [-1];

    private readonly ilCtrlInterface $ctrl;
    private readonly UIFactory $ui_factory;
    private readonly UIRenderer $ui_renderer;
    private readonly ilUIService $ui_service;
    private readonly ServerRequestInterface $request;
    private readonly DataFactory $data_factory;
    private readonly ilUserDefaultsPlugin $pl;
    private readonly StandardFilter $filter;
    private readonly DataTable $table;

    public function __construct(private readonly usrdefUserGUI $parent_obj)
    {
        global $DIC;

        $this->ctrl = $DIC->ctrl();
        $this->ui_factory = $DIC->ui()->factory();
        $this->ui_renderer = $DIC->ui()->renderer();
        $this->ui_service = $DIC->uiService();
        $this->request = $DIC->http()->request();
        $this->data_factory = new DataFactory();
        $this->pl = ilUserDefaultsPlugin::getInstance();

        $this->filter = $this->buildFilter();
        $this->table = $this->buildTable();
    }

    public function render(): string
    {
        return $this->ui_renderer->render([$this->filter, $this->table]);
    }

    /**
     * Every user the current filter matches. Used to resolve a "select all",
     * which hands over a single ALL_OBJECTS marker instead of the ids.
     *
     * @return list<int>
     */
    public function getAllMatchingUserIds(): array
    {
        $total = $this->getTotalRowCount(null, null, null);
        if ($total === 0) {
            return [];
        }

        $query = $this->buildQuery();
        $query->setOffset(0);
        $query->setLimit($total);

        return array_map(
            static fn(array $row): int => (int) $row['usr_id'],
            $query->query()['set']
        );
    }

    public function getRows(
        DataRowBuilder $row_builder,
        array $visible_column_ids,
        Range $range,
        Order $order,
        mixed $additional_viewcontrol_data,
        mixed $filter_data,
        mixed $additional_parameters
    ): Generator {
        [$order_field, $order_direction] = $order->join([], fn($ret, $key, $value): array => [$key, $value]);

        $query = $this->buildQuery();
        $query->setOffset($range->getStart());
        $query->setLimit($range->getLength());
        if (in_array($order_field, self::SORTABLE, true)) {
            $query->setOrderField($order_field);
            $query->setOrderDirection(strtolower((string) $order_direction) === 'desc' ? 'desc' : 'asc');
        }

        foreach ($query->query()['set'] as $record) {
            yield $row_builder->buildDataRow((string) $record['usr_id'], [
                'firstname' => (string) ($record['firstname'] ?? ''),
                'lastname' => (string) ($record['lastname'] ?? ''),
                'email' => (string) ($record['email'] ?? ''),
                'login' => (string) ($record['login'] ?? ''),
            ]);
        }
    }

    public function getTotalRowCount(
        mixed $additional_viewcontrol_data,
        mixed $filter_data,
        mixed $additional_parameters
    ): ?int {
        $query = $this->buildQuery();
        $query->setOffset(0);
        $query->setLimit(1);

        return (int) $query->query()['cnt'];
    }

    protected function buildTable(): DataTable
    {
        return $this->ui_factory->table()->data(
            $this,
            $this->pl->txt('tabs_users'),
            $this->getColumns()
        )->withId(
            self::TABLE_ID
        )->withActions(
            $this->getActions()
        )->withOrder(
            new Order(ilUserQuery::DEFAULT_ORDER_FIELD, Order::ASC)
        )->withRequest($this->request);
    }

    /**
     * @return array<string, \ILIAS\UI\Component\Table\Column\Column>
     */
    protected function getColumns(): array
    {
        $column = $this->ui_factory->table()->column();

        return [
            'firstname' => $column->text($this->pl->txt('usr_firstname')),
            'lastname' => $column->text($this->pl->txt('usr_lastname')),
            'email' => $column->eMail($this->pl->txt('usr_email')),
            'login' => $column->text($this->pl->txt('usr_login')),
        ];
    }

    /**
     * @return array<string, \ILIAS\UI\Component\Table\Action\Action>
     */
    protected function getActions(): array
    {
        $uri = $this->data_factory->uri(
            ILIAS_HTTP_PATH . '/' . $this->ctrl->getLinkTarget(
                $this->parent_obj,
                usrdefUserGUI::CMD_SELECT_USER
            )
        );

        /**
         * @var URLBuilder      $url_builder
         * @var URLBuilderToken $action_token
         * @var URLBuilderToken $row_id_token
         */
        [$url_builder, $action_token, $row_id_token] = (new URLBuilder($uri))->acquireParameters(
            self::NAMESPACE,
            self::TOKEN_ACTION,
            self::TOKEN_ROW_ID
        );

        return [
            self::ACTION_ASSIGN => $this->ui_factory->table()->action()->multi(
                $this->pl->txt('button_select_user'),
                $url_builder->withParameter($action_token, self::ACTION_ASSIGN),
                $row_id_token
            )
        ];
    }

    protected function buildFilter(): StandardFilter
    {
        $field = $this->ui_factory->input()->field();

        $inputs = [
            'query' => $field->text($this->pl->txt('usr_search_term')),
        ];
        $rendered = [true];

        $org_units = $this->buildOrgunitOptions();
        if ($org_units !== []) {
            $inputs['orgu'] = $field->multiSelect($this->pl->txt('usr_orgu'), $org_units);
            $rendered[] = true;
        }

        return $this->ui_service->filter()->standard(
            self::FILTER_ID,
            $this->ctrl->getLinkTarget($this->parent_obj, usrdefUserGUI::CMD_INDEX),
            $inputs,
            $rendered,
            true,
            true
        );
    }

    protected function buildQuery(): ilUserQuery
    {
        $filter_data = $this->ui_service->filter()->getData($this->filter) ?? [];

        $query = new ilUserQuery();

        // ilUserQuery searches login, first-/lastname and both mail addresses at
        // once, so the four separate inputs of the old table collapse into one.
        $search_term = trim((string) ($filter_data['query'] ?? ''));
        if ($search_term !== '') {
            $query->setTextFilter($search_term);
        }

        $org_units = array_filter((array) ($filter_data['orgu'] ?? []));
        if ($org_units !== []) {
            $query->setUserFilter($this->resolveOrgUnitMembers($org_units));
        }

        return $query;
    }

    /**
     * @param list<int|string> $org_unit_ref_ids
     * @return list<int>
     */
    protected function resolveOrgUnitMembers(array $org_unit_ref_ids): array
    {
        // getEmployees() looks the employee position up by its core identifier;
        // the position's database id is not guaranteed to be the same number.
        $tree = ilObjOrgUnitTree::_getInstance();
        $usr_ids = [];
        foreach ($org_unit_ref_ids as $ref_id) {
            $usr_ids = array_merge($usr_ids, $tree->getEmployees((int) $ref_id));
        }
        $usr_ids = array_unique($usr_ids);

        return $usr_ids === [] ? self::MATCHES_NOBODY : array_values(array_map('intval', $usr_ids));
    }

    /**
     * @return array<int, string>
     */
    protected function buildOrgunitOptions(): array
    {
        $root_ref_id = ilObjOrgUnit::getRootOrgRefId();
        if ($root_ref_id === 0) {
            return [];
        }

        $org_units = [];
        foreach (ilObjOrgUnitTree::_getInstance()->getAllChildren($root_ref_id) as $child) {
            if ($child === $root_ref_id) {
                continue;
            }
            $org_units[$child] = ilObject::_lookupTitle(ilObject::_lookupObjId($child));
        }

        return $org_units;
    }
}
