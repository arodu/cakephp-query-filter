<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestCase;

use Cake\ORM\Query;
use Cake\TestSuite\TestCase;
use QueryFilter\Test\Fixture\QueryFilterTestFixture;
use QueryFilter\Test\TestTable\QueryFilterTestTable;

/**
 * Base test case for QueryFilter tests.
 */
abstract class QueryFilterTestCase extends TestCase
{
    /**
     * @var array
     */
    protected $fixtures = [QueryFilterTestFixture::class];

    /**
     * @var \QueryFilter\Test\TestTable\QueryFilterTestTable
     */
    protected QueryFilterTestTable $Table;

    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->Table = $this->getTableLocator()->get('QueryFilterTest', [
            'className' => QueryFilterTestTable::class,
        ]);
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        unset($this->Table);
        $this->getTableLocator()->clear();

        parent::tearDown();
    }

    /**
     * Returns the ids matched by a query, in ascending order.
     *
     * @param \Cake\ORM\Query $query The query.
     * @return array
     */
    protected function ids(Query $query): array
    {
        $ids = [];
        foreach ($query->select(['id'])->orderAsc('id')->all() as $row) {
            $ids[] = $row->id;
        }

        return $ids;
    }
}
