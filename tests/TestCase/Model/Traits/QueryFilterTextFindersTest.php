<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestCase\Model\Traits;

use QueryFilter\QueryFilter;
use QueryFilter\Test\TestCase\QueryFilterTestCase;

/**
 * Tests for the text search finder.
 */
class QueryFilterTextFindersTest extends QueryFilterTestCase
{
    /**
     * @return void
     */
    public function testStringOnSingleField(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_STRING, [
            'tableField' => 'name',
            'value' => 'alice',
        ]);

        $this->assertSame([1], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testStringOnMultipleFields(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_STRING, [
            'tableField' => ['name', 'email'],
            'value' => 'bob',
        ]);

        $this->assertSame([2], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testStringTokenizesMultipleWords(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_STRING, [
            'tableField' => 'name',
            'value' => 'carol white',
        ]);

        $this->assertSame([3], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testStringKeepsQuotedSegmentsTogether(): void
    {
        $query = $this->Table->find(QueryFilter::FINDER_STRING, [
            'tableField' => 'name',
            'value' => '"david brown"',
        ]);

        $this->assertSame([4], $this->ids($query));
    }
}
