<?php

declare(strict_types=1);

namespace QueryFilter\Model\Behavior;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use InvalidArgumentException;
use QueryFilter\Model\Traits\QueryFilterFinders;

/**
 * QueryFilter behavior
 */
class QueryFilterBehavior extends Behavior
{
    use QueryFilterFinders;

    private array $_filterFields = [];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [
        'filterFields' => [],
    ];

    /**
     * @param array $config
     * @return void
     */
    public function initialize(array $config): void
    {
        foreach ($this->getConfig('filterFields') as $key => $options) {
            $this->addFilterField($key, $options);
        }
    }

    /**
     * @param string $key
     * @param array $options
     * @return void
     */
    public function addFilterField(string $key, array $options = [])
    {
        $options['tableField'] = $options['tableField'] ?? null;

        if (empty($options['finder'])) {
            throw new InvalidArgumentException('param finder is necessary on options');
        }

        if (is_string($options['finder']) && !$this->table()->hasFinder($options['finder'])) {
            throw new NotFoundException(__('Finder {0} not found on table class {1}', $options['finder'], $this->table()::class));
        }

        $this->_filterFields[$key] = $options;
    }

    /**
     * @param Query $query
     * @param array $formData
     * @return Query
     */
    public function queryFilter(Query $query, array $formData = []): Query
    {
        $inputFields = $this->checkInputFields($formData);

        foreach ($inputFields as $key => $value) {
            $query = $this->handleFinder($query, $key, $value);
        }

        return $query;
    }

    /**
     * @param Query $query
     * @param string $key
     * @param mixed $value
     * @return Query
     */
    protected function handleFinder(Query $query, string $key, mixed $value): Query
    {
        $filterField = $this->getFilterField($key);

        if (empty($filterField)) {
            return $query;
        }

        $finder = $filterField['finder'];
        unset($filterField['finder']);

        $options = array_merge($filterField, [
            'key' => $key,
            'value' => $value,
        ]);

        if (is_callable($finder)) {
            $query = $finder($query, $options);
        } elseif (is_string($finder)) {
            $query = $query->find($finder, $options);
        } else {
            throw new NotFoundException('Finder cannot be recognized');
        }

        return $query;
    }

    /**
     * @param string $key
     * @return array|null
     */
    protected function getFilterField(string $key): ?array
    {
        return $this->_filterFields[$key] ?? null;
    }

    /**
     * @param array $filter
     * @return array
     */
    protected function checkInputFields(array $formData): array
    {
        return array_filter($formData, function ($v, $k) {
            return !empty($v);
        }, ARRAY_FILTER_USE_BOTH);
    }
}
