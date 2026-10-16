<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestCase\Model\Traits;

use Cake\Chronos\Chronos;
use QueryFilter\QueryFilter;
use QueryFilter\Test\TestCase\QueryFilterTestCase;

/**
 * Tests for the date finders.
 *
 * The fixture dates are fixed around 2024-06-15, and the tests freeze "now" to
 * 2024-06-15 10:00:00.
 */
class QueryFilterDatesFindersTest extends QueryFilterTestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Chronos::setTestNow('2024-06-15 10:00:00');
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Chronos::setTestNow();

        parent::tearDown();
    }

    /**
     * @return void
     */
    public function testIsToday(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_IS_TODAY, ['tableField' => 'created']);

        $this->assertSame([2, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testIsTomorrow(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_IS_TOMORROW, ['tableField' => 'created']);

        $this->assertSame([3], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testIsYesterday(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_IS_YESTERDAY, ['tableField' => 'created']);

        $this->assertSame([1], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testIsPast(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_IS_PAST, ['tableField' => 'created']);

        $this->assertSame([1, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testIsFuture(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_IS_FUTURE, ['tableField' => 'created']);

        $this->assertSame([2, 3], $this->ids($query));
    }
}
