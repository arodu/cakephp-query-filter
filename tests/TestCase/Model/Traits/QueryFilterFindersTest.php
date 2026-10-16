<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestCase\Model\Traits;

use InvalidArgumentException;
use QueryFilter\QueryFilter;
use QueryFilter\Test\TestCase\QueryFilterTestCase;

/**
 * Tests for the equality and set finders.
 */
class QueryFilterFindersTest extends QueryFilterTestCase
{
    /**
     * @return void
     */
    public function testEqual(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_EQUAL, [
            'tableField' => 'status',
            'value' => 'active',
        ]);

        $this->assertSame([1, 3], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testNotEqual(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_NOT_EQUAL, [
            'tableField' => 'status',
            'value' => 'active',
        ]);

        $this->assertSame([2, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testIn(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_IN, [
            'tableField' => 'status',
            'value' => ['active', 'pending'],
        ]);

        $this->assertSame([1, 3, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testNotIn(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_NOT_IN, [
            'tableField' => 'status',
            'value' => ['active'],
        ]);

        $this->assertSame([2, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testSelectWithScalar(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_SELECT, [
            'tableField' => 'status',
            'value' => 'inactive',
        ]);

        $this->assertSame([2], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testSelectWithArray(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_SELECT, [
            'tableField' => 'status',
            'value' => ['active', 'pending'],
        ]);

        $this->assertSame([1, 3, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testMultiSelect(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_MULTI_SELECT, [
            'tableField' => 'status',
            'value' => ['inactive', 'pending'],
        ]);

        $this->assertSame([2, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testEqualThrowsWithoutTableField(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Table->find(QueryFilter::FINDER_EQUAL, ['value' => 'active']);
    }

    /**
     * @return void
     */
    public function testEqualThrowsWithoutValue(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->Table->find(QueryFilter::FINDER_EQUAL, ['tableField' => 'status']);
    }
}
