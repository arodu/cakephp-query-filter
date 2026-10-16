<?php
declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\ORM\Query;
use Cake\Utility\Text;
use QueryFilter\QueryFilter;

/**
 * Text search finders.
 */
trait QueryFilterTextFindersTrait
{
    use QueryFilterConditionsTrait;

    /**
     * Matches rows where any field contains every token of the value.
     *
     * The value is tokenized on spaces; quoted segments are kept together.
     *
     * @param \Cake\ORM\Query $query The query.
     * @param array $options Options: `tableField`, `value`, `templateField`, `templateValue`.
     * @return \Cake\ORM\Query
     */
    public function findQueryFilterString(Query $query, array $options = []): Query
    {
        $fields = $this->requireTableField($options);
        $value = (string)$this->requireValue($options);

        $tokens = Text::tokenize(preg_replace('/\s+/', ' ', trim($value)) ?? '', ' ', '"', '"');
        $tokens = array_values(array_filter(
            array_map(fn(string $token): string => trim($token, '"'), $tokens),
            fn(string $token): bool => $token !== ''
        ));

        $options += [
            'templateField' => QueryFilter::FIELD_TEMPLATE_LIKE,
            'templateValue' => QueryFilter::STRING_TEMPLATE_LIKE_INNER,
        ];

        return $query->where(['OR' => $this->mapConditions($fields, $tokens, $options)]);
    }
}
