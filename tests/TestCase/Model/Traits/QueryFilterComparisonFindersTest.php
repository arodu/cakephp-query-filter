<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestCase\Model\Traits;

use InvalidArgumentException;
use QueryFilter\QueryFilter;
use QueryFilter\Test\TestCase\QueryFilterTestCase;

/**
 * Tests for the comparison finders.
 */
class QueryFilterComparisonFindersTest extends QueryFilterTestCase
{
    /**
     * @return void
     */
    public function testGreaterThan(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_GREATER_THAN, [
            'tableField' => 'age',
            'value' => 30,
        ]);

        $this->assertSame([4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testGreaterThanOrEqual(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_GREATER_THAN_OR_EQUAL, [
            'tableField' => 'age',
            'value' => 30,
        ]);

        $this->assertSame([1, 3, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testLessThan(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_LESS_THAN, [
            'tableField' => 'age',
            'value' => 30,
        ]);

        $this->assertSame([2], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testLessThanOrEqual(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_LESS_THAN_OR_EQUAL, [
            'tableField' => 'age',
            'value' => 30,
        ]);

        $this->assertSame([1, 2, 3], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testComparisonRejectsArrayValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Table->find(QueryFilter::FINDER_GREATER_THAN, [
            'tableField' => 'age',
            'value' => [1, 2],
        ]);
    }
}
