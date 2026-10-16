<?php
declare(strict_types=1);

namespace QueryFilter\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * Fixture for the QueryFilter test table.
 *
 * Dates are fixed so the date finders can be tested with a frozen "now"
 * (`2024-06-15 10:00:00`).
 */
class QueryFilterTestFixture extends TestFixture
{
    /**
     * The schema is created by tests/schema.sql (SchemaLoader); this fixture
     * is data-only.
     *
     * @var array
     */
    public array $fields = [];

    /**
     * @var array
     */
    public array $records = [
        [
            'id' => 1,
            'name' => 'Alice Smith',
            'email' => 'alice@example.com',
            'age' => 30,
            'score' => 100,
            'status' => 'active',
            'created' => '2024-06-14 09:00:00',
            'modified' => '2024-06-14 09:00:00',
        ],
        [
            'id' => 2,
            'name' => 'Bob Jones',
            'email' => 'bob@example.com',
            'age' => 25,
            'score' => 80,
            'status' => 'inactive',
            'created' => '2024-06-15 12:30:00',
            'modified' => '2024-06-15 12:30:00',
        ],
        [
            'id' => 3,
            'name' => 'Carol White',
            'email' => 'carol@example.com',
            'age' => 30,
            'score' => 100,
            'status' => 'active',
            'created' => '2024-06-16 23:00:00',
            'modified' => '2024-06-16 23:00:00',
        ],
        [
            'id' => 4,
            'name' => 'David Brown',
            'email' => 'david@example.com',
            'age' => 40,
            'score' => 60,
            'status' => 'pending',
            'created' => '2024-06-15 08:00:00',
            'modified' => '2024-06-15 08:00:00',
        ],
    ];
}
