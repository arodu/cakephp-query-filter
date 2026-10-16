<?php
declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\Chronos\Chronos;
use Cake\ORM\Query\SelectQuery;

/**
 * Date finders: today, tomorrow, yesterday, past and future.
 */
trait QueryFilterDatesFindersTrait
{
    use QueryFilterConditionsTrait;

    /**
     * Matches rows whose field falls within today.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterIsToday(SelectQuery $query, array $options = []): SelectQuery
    {
        return $this->betweenDay($query, $options, Chronos::today());
    }

    /**
     * Matches rows whose field falls within tomorrow.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterIsTomorrow(SelectQuery $query, array $options = []): SelectQuery
    {
        return $this->betweenDay($query, $options, Chronos::tomorrow());
    }

    /**
     * Matches rows whose field falls within yesterday.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterIsYesterday(SelectQuery $query, array $options = []): SelectQuery
    {
        return $this->betweenDay($query, $options, Chronos::yesterday());
    }

    /**
     * Matches rows whose field is before now.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterIsPast(SelectQuery $query, array $options = []): SelectQuery
    {
        return $this->compareNow($query, $options, '<');
    }

    /**
     * Matches rows whose field is after now.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterIsFuture(SelectQuery $query, array $options = []): SelectQuery
    {
        return $this->compareNow($query, $options, '>');
    }

    /**
     * Builds a full-day range condition (`field >= start AND field <= end`).
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options The finder options.
     * @param \Cake\Chronos\Chronos $day The day to match.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function betweenDay(SelectQuery $query, array $options, Chronos $day): SelectQuery
    {
        $fields = $this->requireTableField($options);
        $start = (clone $day)->startOfDay();
        $end = (clone $day)->endOfDay();

        $conditions = [];
        foreach ((array)$fields as $field) {
            $conditions[] = [$field . ' >=' => $start, $field . ' <=' => $end];
        }

        return $query->where($conditions);
    }

    /**
     * Builds a comparison against now (`field OP now`).
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options The finder options.
     * @param string $operator The comparison operator.
     * @return \Cake\ORM\Query\SelectQuery
     */
    protected function compareNow(SelectQuery $query, array $options, string $operator): SelectQuery
    {
        $fields = $this->requireTableField($options);
        $now = Chronos::now();

        $conditions = [];
        foreach ((array)$fields as $field) {
            $conditions[] = [$field . ' ' . $operator => $now];
        }

        return $query->where($conditions);
    }
}
