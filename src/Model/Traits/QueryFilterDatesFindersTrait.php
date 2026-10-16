<?php
declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\Chronos\Chronos;
use Cake\ORM\Query;

/**
 * Date finders: today, tomorrow, yesterday, past and future.
 */
trait QueryFilterDatesFindersTrait
{
    use QueryFilterConditionsTrait;

    /**
     * Matches rows whose field falls within today.
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterIsToday(Query $query, array $options = []): Query
    {
        return $this->betweenDay($query, $options, Chronos::today());
    }

    /**
     * Matches rows whose field falls within tomorrow.
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterIsTomorrow(Query $query, array $options = []): Query
    {
        return $this->betweenDay($query, $options, Chronos::tomorrow());
    }

    /**
     * Matches rows whose field falls within yesterday.
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterIsYesterday(Query $query, array $options = []): Query
    {
        return $this->betweenDay($query, $options, Chronos::yesterday());
    }

    /**
     * Matches rows whose field is before now.
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterIsPast(Query $query, array $options = []): Query
    {
        return $this->compareNow($query, $options, '<');
    }

    /**
     * Matches rows whose field is after now.
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterIsFuture(Query $query, array $options = []): Query
    {
        return $this->compareNow($query, $options, '>');
    }

    /**
     * Builds a full-day range condition (`field >= start AND field <= end`).
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options The finder options.
     * @param \Cake\Chronos\Chronos $day The day to match.
     * @return \Cake\ORM\Query
     */
    protected function betweenDay(Query $query, array $options, Chronos $day): Query
    {
        $fields = $this->requireTableField($options);
        $start = $day->copy()->startOfDay();
        $end = $day->copy()->endOfDay();

        $conditions = [];
        foreach ((array)$fields as $field) {
            $conditions[] = [$field . ' >=' => $start, $field . ' <=' => $end];
        }

        return $query->where($conditions);
    }

    /**
     * Builds a comparison against now (`field OP now`).
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options The finder options.
     * @param string $operator The comparison operator.
     * @return \Cake\ORM\Query
     */
    protected function compareNow(Query $query, array $options, string $operator): Query
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
