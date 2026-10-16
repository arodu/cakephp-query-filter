<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestTable;

use Cake\ORM\Query;
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
     * @param \Cake\ORM\Query $query The query.
     * @param array $options The finder options.
     * @return \Cake\ORM\Query
     */
    public function findAgeAbove(Query $query, array $options = []): Query
    {
        return $query->where(['age >' => $options['value']]);
    }
}
