<?php
declare(strict_types=1);

namespace QueryFilter\Test\TestCase\Model\Behavior;

use Cake\Event\Event;
use Cake\Http\Exception\NotFoundException;
use Cake\ORM\Query\SelectQuery;
use InvalidArgumentException;
use QueryFilter\QueryFilter;
use QueryFilter\Test\TestCase\QueryFilterTestCase;

/**
 * Tests for the QueryFilter behavior.
 */
class QueryFilterBehaviorTest extends QueryFilterTestCase
{
    /**
     * @return void
     */
    public function testAddFilterFieldRequiresFinder(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->behavior()->addFilterField('name', ['tableField' => 'name']);
    }

    /**
     * @return void
     */
    public function testAddFilterFieldWithUnknownFinder(): void
    {
        $this->expectException(NotFoundException::class);

        $this->behavior()->addFilterField('name', ['finder' => 'doesNotExist']);
    }

    /**
     * @return void
     */
    public function testQueryFilterAppliesRegisteredFields(): void
    {
        $this->behavior()->addFilterField('name', [
            'tableField' => 'name',
            'finder' => QueryFilter::FINDER_STRING,
        ]);

        $query = $this->behavior()->queryFilter($this->Table->find(), ['name' => 'alice']);

        $this->assertSame([1], $this->ids($query));
        $this->assertTrue($this->behavior()->queryWasFiltered());
        $this->assertSame(1, $this->behavior()->queryFiltered());
        $this->assertSame(['name' => 'alice'], $this->behavior()->appliedFilters());
    }

    /**
     * @return void
     */
    public function testQueryFilterKeepsZeroValue(): void
    {
        $this->behavior()->addFilterField('score', [
            'tableField' => 'score',
            'finder' => QueryFilter::FINDER_EQUAL,
        ]);

        $this->behavior()->queryFilter($this->Table->find(), ['score' => '0']);

        $this->assertSame(1, $this->behavior()->queryFiltered());
    }

    /**
     * @return void
     */
    public function testQueryFilterDropsEmptyValues(): void
    {
        $this->behavior()->addFilterField('name', [
            'tableField' => 'name',
            'finder' => QueryFilter::FINDER_STRING,
        ]);

        $this->behavior()->queryFilter($this->Table->find(), ['name' => '']);

        $this->assertFalse($this->behavior()->queryWasFiltered());
        $this->assertSame(0, $this->behavior()->queryFiltered());
    }

    /**
     * @return void
     */
    public function testQueryFilterIgnoresUnregisteredKeys(): void
    {
        $this->behavior()->queryFilter($this->Table->find(), ['unknown' => 'value']);

        $this->assertFalse($this->behavior()->queryWasFiltered());
    }

    /**
     * @return void
     */
    public function testQueryFilterWithCallableFinder(): void
    {
        $this->behavior()->addFilterField('age', [
            'finder' => function (SelectQuery $query, array $options): SelectQuery {
                return $query->where(['age >=' => $options['value']]);
            },
        ]);

        $query = $this->behavior()->queryFilter($this->Table->find(), ['age' => 30]);

        $this->assertSame([1, 3, 4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testQueryFilterWithStringFinder(): void
    {
        $this->behavior()->addFilterField('age', ['finder' => 'ageAbove']);

        $query = $this->behavior()->queryFilter($this->Table->find(), ['age' => 30]);

        $this->assertSame([4], $this->ids($query));
    }

    /**
     * @return void
     */
    public function testAfterFilterEventIsDispatched(): void
    {
        $this->behavior()->addFilterField('status', [
            'tableField' => 'status',
            'finder' => QueryFilter::FINDER_EQUAL,
        ]);

        $captured = null;
        $this->Table->getEventManager()->on(
            QueryFilter::EVENT_AFTER_FILTER,
            function (Event $event) use (&$captured): void {
                $captured = $event->getData();
            },
        );

        $this->behavior()->queryFilter($this->Table->find(), ['status' => 'active'], ['key' => 'students.index']);

        $this->assertNotNull($captured);
        $this->assertSame('students.index', $captured['key']);
        $this->assertSame(['status' => 'active'], $captured['data']);
        $this->assertSame(1, $captured['count']);
    }

    /**
     * @return void
     */
    public function testBeforeFilterEventIsDispatched(): void
    {
        $captured = null;
        $this->Table->getEventManager()->on(
            QueryFilter::EVENT_BEFORE_FILTER,
            function (Event $event) use (&$captured): void {
                $captured = $event->getData();
            },
        );

        $this->behavior()->queryFilter($this->Table->find(), ['whatever' => 'value'], ['key' => 'group']);

        $this->assertNotNull($captured);
        $this->assertSame('group', $captured['key']);
        $this->assertSame(['whatever' => 'value'], $captured['data']);
    }

    /**
     * @return void
     */
    public function testBeforeFinderEventIsDispatched(): void
    {
        $this->behavior()->addFilterField('status', [
            'tableField' => 'status',
            'finder' => QueryFilter::FINDER_EQUAL,
        ]);

        $called = false;
        $this->Table->getEventManager()->on(
            QueryFilter::EVENT_BEFORE_FINDER,
            function () use (&$called): void {
                $called = true;
            },
        );

        $this->behavior()->queryFilter($this->Table->find(), ['status' => 'active']);

        $this->assertTrue($called);
    }
}
