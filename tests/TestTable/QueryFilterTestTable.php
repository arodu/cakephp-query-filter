<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestTable;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\Table;

/**
 * Test table exposing the QueryFilter behavior.
 */
class QueryFilterTestTable extends Table
{
    /**
     * @param array $config Table configuration.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('query_filter_tests');
        $this->addBehavior('QueryFilter.QueryFilter');
    }

    /**
     * Custom finder used to test string (named) finders.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options The finder options.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findAgeAbove(SelectQuery $query, array $options = []): SelectQuery
    {
        return $query->where(['age >' => $options['value']]);
    }
}
