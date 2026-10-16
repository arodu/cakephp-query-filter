<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestCase;

use Cake\ORM\Query\SelectQuery;
use Cake\TestSuite\TestCase;
use QueryFilter\Model\Behavior\QueryFilterBehavior;
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
    protected array $fixtures = [QueryFilterTestFixture::class];

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
     * Returns the QueryFilter behavior attached to the test table.
     *
     * @return \QueryFilter\Model\Behavior\QueryFilterBehavior
     */
    protected function behavior(): QueryFilterBehavior
    {
        return $this->Table->getBehavior('QueryFilter');
    }

    /**
     * Returns the ids matched by a query, in ascending order.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @return array
     */
    protected function ids(SelectQuery $query): array
    {
        $ids = [];
        foreach ($query->select(['id'])->orderByAsc('id')->all() as $row) {
            $ids[] = $row->id;
        }

        return $ids;
    }
}
