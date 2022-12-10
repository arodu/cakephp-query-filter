<?php

declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\ORM\Query;
use DateTimeInterface;
use InvalidArgumentException;

// @todo WIP
trait QueryFilterDatesFinders
{
    public function findQueryFilterQreaterThan(Query $query, array $options = []): Query
    {
        if (!($options['value'] instanceof DateTimeInterface)) {
            throw new InvalidArgumentException('param value needs to be an \DateTimeInterface object');
        }

        return $query;
    }

    public function findQueryFilterQreaterThanOrEqual(Query $query, array $options = []): Query
    {
        return $query;
    }

    public function findQueryFilterLessThan(Query $query, array $options = []): Query
    {
        return $query;
    }

    public function findQueryFilterLessThanOrEqual(Query $query, array $options = []): Query
    {
        return $query;
    }

    public function findQueryFilterIsToday(Query $query, array $options = []): Query
    {
        return $query;
    }

    public function findQueryFilterIsTomorrow(Query $query, array $options = []): Query
    {
        return $query;
    }

    public function findQueryFilterIsYesterday(Query $query, array $options = []): Query
    {
        return $query;
    }

    public function findQueryFilterIsPast(Query $query, array $options = []): Query
    {
        return $query;
    }

    public function findQueryFilterIsFuture(Query $query, array $options = []): Query
    {
        return $query;
    }
}
