<?php
declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\ORM\Query;
use InvalidArgumentException;
use QueryFilter\QueryFilter;

/**
 * Comparison finders (`>`, `>=`, `<`, `<=`). Usable with numbers and dates.
 */
trait QueryFilterComparisonFindersTrait
{
    use QueryFilterConditionsTrait;

    /**
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterGreaterThan(Query $query, array $options = []): Query
    {
        return $this->compare($query, $options, QueryFilter::FIELD_TEMPLATE_GREATER_THAN);
    }

    /**
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterGreaterThanOrEqual(Query $query, array $options = []): Query
    {
        return $this->compare($query, $options, QueryFilter::FIELD_TEMPLATE_GREATER_THAN_OR_EQUAL);
    }

    /**
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterLessThan(Query $query, array $options = []): Query
    {
        return $this->compare($query, $options, QueryFilter::FIELD_TEMPLATE_LESS_THAN);
    }

    /**
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterLessThanOrEqual(Query $query, array $options = []): Query
    {
        return $this->compare($query, $options, QueryFilter::FIELD_TEMPLATE_LESS_THAN_OR_EQUAL);
    }

    /**
     * Applies a comparison operator on the given field(s).
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options The finder options.
     * @param string $templateField The field template holding the operator.
     * @return \Cake\ORM\Query
     * @throws \InvalidArgumentException When the value is an array.
     */
    protected function compare(Query $query, array $options, string $templateField): Query
    {
        $fields = $this->requireTableField($options);
        $value = $this->requireValue($options);

        if (is_array($value)) {
            throw new InvalidArgumentException('The "value" option must be a scalar or a DateTimeInterface.');
        }

        $options += ['templateField' => $templateField];

        return $query->where($this->mapFieldConditions($fields, $value, $options));
    }
}
