<?php

declare(strict_types=1);

namespace QueryFilter\Model\Behavior;

use Cake\Core\Configure;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Behavior;
use Cake\ORM\Query;
use QueryFilter\Model\Traits\BasicFinders;

/**
 * QueryFilter behavior
 */
class QueryFilterBehavior extends Behavior
{
    use BasicFinders;

    protected array $_filterFields = [];

    /**
     * Default configuration.
     *
     * @var array<string, mixed>
     */
    protected $_defaultConfig = [
        'filterFields' => [],
    ];

    public function initialize(array $config): void
    {
        foreach ($this->getConfig('filterFields') as $key => $options) {
            $this->addFilterField($key, $options);
        }
    }

    public function addFilterField(string $key, array $options = [])
    {
        $options['tableField'] = $options['tableField'] ?? null;


        // @todo set default option values
        $this->_filterFields[$key] = $options;
    }

    public function queryFilter(Query $query, array $formData = []): Query
    {
        $inputFields = $this->checkInputFields($formData);

        foreach ($inputFields as $key => $value) {
            $query = $this->handleFinder($query, $key, $value);
        }

        return $query;
    }

    protected function getFilterField(string $key): ?array
    {
        return $this->_filterFields[$key] ?? null;
    }

    protected function handleFinder(Query $query, $key, $value)
    {
        $filterField = $this->getFilterField($key);

        if (empty($filterField)) {
            return $query;
        }

        $finder = $filterField['finder'];
        unset($filterField['finder']);

        if (is_string($finder) && !$this->table()->hasFinder($finder)) {
            throw new NotFoundException(__('Finder {0} not found on table class {1}', $filterField['finder'], $this->table()::class));
        }

        $options = array_merge($filterField, [
            'key' => $key,
            'value' => $value,
        ]);

        if (is_callable($finder)) {
            $query = $finder($query, $options);
        } else {
            $query = $query->find($finder, $options);
        }

        return $query;
    }

    protected function checkInputFields(array $filter): array
    {
        return array_filter($filter, function ($v, $k) {
            return !empty($v);
        }, ARRAY_FILTER_USE_BOTH);
    }
}
