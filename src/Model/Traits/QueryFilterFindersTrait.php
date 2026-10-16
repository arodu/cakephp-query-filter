<?php
declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\ORM\Query\SelectQuery;
use QueryFilter\QueryFilter;

/**
 * Equality and set based finders.
 */
trait QueryFilterFindersTrait
{
    use QueryFilterConditionsTrait;

    /**
     * Matches rows where the field equals the value.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterEqual(SelectQuery $query, array $options = []): SelectQuery
    {
        $fields = $this->requireTableField($options);
        $value = $this->requireValue($options);

        return $query->where($this->mapFieldConditions($fields, $value, $options));
    }

    /**
     * Matches rows where the field does not equal the value.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterNotEqual(SelectQuery $query, array $options = []): SelectQuery
    {
        $fields = $this->requireTableField($options);
        $value = $this->requireValue($options);
        $options += ['templateField' => QueryFilter::FIELD_TEMPLATE_NOT_EQUAL];

        return $query->where($this->mapFieldConditions($fields, $value, $options));
    }

    /**
     * Matches rows where the field is in the given set of values.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`, `value` (array), `templateField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterIn(SelectQuery $query, array $options = []): SelectQuery
    {
        $fields = $this->requireTableField($options);
        $values = (array)$this->requireValue($options);
        $options += ['templateField' => QueryFilter::FIELD_TEMPLATE_IN];

        return $query->where(['OR' => $this->mapFieldConditions($fields, $values, $options)]);
    }

    /**
     * Matches rows where the field is not in the given set of values.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`, `value` (array), `templateField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterNotIn(SelectQuery $query, array $options = []): SelectQuery
    {
        $fields = $this->requireTableField($options);
        $values = (array)$this->requireValue($options);
        $options += ['templateField' => QueryFilter::FIELD_TEMPLATE_NOT_IN];

        return $query->where(['AND' => $this->mapFieldConditions($fields, $values, $options)]);
    }

    /**
     * Select finder: equals when the value is scalar, in when it is an array.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterSelect(SelectQuery $query, array $options = []): SelectQuery
    {
        return is_array($this->requireValue($options))
            ? $this->findQueryFilterIn($query, $options)
            : $this->findQueryFilterEqual($query, $options);
    }

    /**
     * Multi-select finder: matches any of the submitted values.
     *
     * @param \Cake\ORM\Query\SelectQuery $query The query.
     * @param array $options Options: `tableField`, `value` (array), `templateField`.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findQueryFilterMultiSelect(SelectQuery $query, array $options = []): SelectQuery
    {
        return $this->findQueryFilterIn($query, $options);
    }
}
