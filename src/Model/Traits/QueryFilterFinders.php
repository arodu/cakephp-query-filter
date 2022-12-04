<?php

declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\ORM\Query;
use Cake\Utility\Hash;
use Cake\Utility\Text;
use InvalidArgumentException;
use QueryFilter\QueryFilterPlugin;

trait QueryFilterFinders
{
    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findQueryFilterEqual(Query $query, array $options = []): Query
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('param tableField is necessary on options');
        }

        $conditions = $this->mapConditions(
            $options['tableField'],
            $options['value']
        );

        return $query->where($conditions);
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findQueryFilterSelect(Query $query, array $options = []): Query
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('param tableField is necessary on options');
        }

        $conditions = $this->mapConditions(
            $options['tableField'],
            $options['value']
        );

        return $query->where($conditions);
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findQueryFilterMultiSelect(Query $query, array $options = []): Query
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('param tableField is necessary on options');
        }

        $conditions = $this->mapConditions(
            $options['tableField'],
            $options['value']
        );

        return $query->where(['OR' => $conditions]);
    }

    /**
     * @param Query $query
     * @param array $options
     * @return Query
     */
    public function findQueryFilterString(Query $query, array $options = []): Query
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('param tableField is necessary on options');
        }

        $values = Text::tokenize(preg_replace('/\s+/', ' ', $options['value']), ' ', '"', '"');

        $conditions = $this->mapConditions(
            $options['tableField'],
            $values,
            [
                'templates.field' => Hash::get($options, 'templates.field', QueryFilterPlugin::FIELD_TEMPLATE_LIKE),
                'templates.value' => Hash::get($options, 'templates.value', QueryFilterPlugin::STRING_TEMPLATE_LIKE_INNER),
            ]
        );

        return $query->where(['OR' => $conditions]);
    }

    /**
     * @param string|array $fields
     * @param string|array $values
     * @param array $options
     * @return array
     */
    protected function mapConditions(string|array $fields, string|array $values, array $options = []): array
    {
        $fieldTemplate = Hash::get($options, 'templates.field', QueryFilterPlugin::FIELD_TEMPLATE_DEFAULT);
        $valueTemplate = Hash::get($options, 'templates.value', QueryFilterPlugin::STRING_TEMPLATE_DEFAULT);
        $fields = is_string($fields) ? [$fields] : $fields;
        $values = is_string($values) ? [$values] : $values;

        $conditions = [];
        foreach ($values as $value) {
            $formatedValue = $this->formatTemplate($valueTemplate, ['content' => $value]);
            foreach ($fields as $field) {
                $formatedField = $this->formatTemplate($fieldTemplate, ['content' => $field]);
                $conditions[] = [$formatedField => $formatedValue];
            }
        }

        return $conditions;
    }

    /**
     * @param string $template
     * @param array $data
     * @param array $options
     * @return string
     */
    protected function formatTemplate(string $template, array $data, array $options = []): string
    {
        $options += ['before' => '{', 'after' => '}'];

        return Text::insert($template, $data, $options);
    }
}
