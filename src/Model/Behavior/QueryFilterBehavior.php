<?php
declare(strict_types=1);

namespace QueryFilter\Model\Behavior;

use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Behavior;
use Cake\ORM\Query\SelectQuery;
use InvalidArgumentException;
use QueryFilter\Model\Traits\QueryFilterAllFindersTrait;
use QueryFilter\QueryFilter;

/**
 * QueryFilter behavior.
 *
 * Maps form input keys to table finders and applies them to a query. Each
 * filter field is registered through `addFilterField()` (or the `filterFields`
 * config) with a `finder` (a table finder name or a callable) and, usually, a
 * `tableField`.
 *
 * Events (all dispatched on the table's event manager):
 * - `QueryFilter.beforeFilter` — before applying the filters.
 * - `QueryFilter.afterFilter` — after applying the filters.
 * - `QueryFilter.beforeFinder` — before applying a single field finder.
 * - `QueryFilter.afterFinder` — after applying a single field finder.
 */
class QueryFilterBehavior extends Behavior
{
    use QueryFilterAllFindersTrait;

    /**
     * Registered filter fields, keyed by their input key.
     *
     * @var array
     */
    protected array $_filterFields = [];

    /**
     * Number of filters applied by the last `queryFilter()` call.
     *
     * @var int
     */
    protected int $_filtered = 0;

    /**
     * Filters applied by the last `queryFilter()` call (input key => value).
     *
     * @var array
     */
    protected array $_applied = [];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected array $_defaultConfig = [
        'filterFields' => [],
        'filterKey' => QueryFilter::DEFAULT_FILTER_KEY,
        'request' => null,
    ];

    /**
     * @param array $config Behavior configuration.
     * @return void
     */
    public function initialize(array $config): void
    {
        foreach ($this->getConfig('filterFields') as $key => $options) {
            $this->addFilterField($key, $options);
        }
    }

    /**
     * Registers a filter field.
     *
     * @param string $key The input key.
     * @param array $options Options: `finder` (required), `tableField`, `templateField`, `templateValue`.
     * @return void
     * @throws \InvalidArgumentException When `finder` is missing.
     * @throws \Cake\Http\Exception\NotFoundException When the finder does not exist on the table.
     */
    public function addFilterField(string $key, array $options = []): void
    {
        $options['tableField'] = $options['tableField'] ?? null;

        if (empty($options['finder'])) {
            throw new InvalidArgumentException('The "finder" option is required.');
        }

        if (is_string($options['finder']) && !$this->table()->hasFinder($options['finder'])) {
            throw new NotFoundException(
                __('Finder {0} not found on table class {1}', $options['finder'], $this->table()::class),
            );
        }

        $this->_filterFields[$key] = $options;
    }

    /**
     * Applies every matching filter field to the query.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $formData The submitted filter data (input key => value).
     * @param array $options Options: `key` (e.g. recent-filters group), `request`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function queryFilter(SelectQuery $query, array $formData = [], array $options = []): SelectQuery
    {
        $this->_filtered = 0;
        $this->_applied = [];

        $this->dispatch(QueryFilter::EVENT_BEFORE_FILTER, $this->eventData($options, [
            'data' => $formData,
        ]));

        foreach ($this->checkInputFields($formData) as $key => $value) {
            $query = $this->handleFinder($query, $key, $value);
        }

        $this->dispatch(QueryFilter::EVENT_AFTER_FILTER, $this->eventData($options, [
            'data' => $this->_applied,
            'count' => $this->_filtered,
        ]));

        return $query;
    }

    /**
     * Whether the last `queryFilter()` call applied at least one filter.
     *
     * @return bool
     */
    public function queryWasFiltered(): bool
    {
        return $this->_filtered > 0;
    }

    /**
     * Number of filters applied by the last `queryFilter()` call.
     *
     * @return int
     */
    public function queryFiltered(): int
    {
        return $this->_filtered;
    }

    /**
     * Filters applied by the last `queryFilter()` call (input key => value).
     *
     * @return array
     */
    public function appliedFilters(): array
    {
        return $this->_applied;
    }

    /**
     * Applies a single filter field to the query.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param string $key The input key.
     * @param mixed $value The submitted value.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function handleFinder(SelectQuery $query, string $key, mixed $value): SelectQuery
    {
        $filterField = $this->getFilterField($key);

        if (empty($filterField)) {
            return $query;
        }

        $finder = $filterField['finder'];
        unset($filterField['finder']);

        $finderOptions = array_merge($filterField, [
            'key' => $key,
            'value' => $value,
        ]);

        $this->dispatch(QueryFilter::EVENT_BEFORE_FINDER, $this->eventData([], [
            'key' => $key,
            'value' => $value,
            'finder' => $finder,
            'options' => $finderOptions,
        ]));

        if (is_callable($finder)) {
            $query = $finder($query, $finderOptions);
        } elseif (is_string($finder)) {
            $method = 'find' . ucfirst($finder);
            if (method_exists($this, $method)) {
                $query = $this->{$method}($query, $finderOptions);
            } else {
                $query = $query->find($finder, ...$finderOptions);
            }
        } else {
            throw new NotFoundException('Finder cannot be recognized.');
        }

        $this->_filtered++;
        $this->_applied[$key] = $value;

        $this->dispatch(QueryFilter::EVENT_AFTER_FINDER, $this->eventData([], [
            'key' => $key,
            'value' => $value,
            'finder' => $finder,
            'query' => $query,
        ]));

        return $query;
    }

    /**
     * Builds the common event data.
     *
     * @param array $options Options: `key`, `request`.
     * @param array $data Event specific data.
     * @return array
     */
    protected function eventData(array $options, array $data = []): array
    {
        $table = $this->table();

        return $data + [
            'table' => $table,
            'alias' => $table->getAlias(),
            'key' => $options['key'] ?? $this->getConfig('filterKey'),
            'request' => $options['request'] ?? $this->getConfig('request'),
        ];
    }

    /**
     * Dispatches an event on the table's event manager.
     *
     * @param string $name The event name.
     * @param array $data The event data.
     * @return void
     */
    protected function dispatch(string $name, array $data): void
    {
        $this->table()->getEventManager()->dispatch(new Event($name, $this->table(), $data));
    }

    /**
     * Returns a registered filter field.
     *
     * @param string $key The input key.
     * @return array|null
     */
    protected function getFilterField(string $key): ?array
    {
        return $this->_filterFields[$key] ?? null;
    }

    /**
     * Keeps only the submitted values that are worth filtering by.
     *
     * `null`, empty strings and empty arrays are dropped; `0`, `'0'` and other
     * falsy-but-meaningful values are kept.
     *
     * @param array $formData The submitted data.
     * @return array
     */
    protected function checkInputFields(array $formData): array
    {
        return array_filter($formData, function ($value): bool {
            return $value !== null && $value !== '' && $value !== [];
        });
    }
}
