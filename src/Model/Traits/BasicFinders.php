<?php

declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\ORM\Query;
use Cake\Utility\Text;
use InvalidArgumentException;
use QueryFilter\QueryFilterPlugin;

trait BasicFinders
{
    protected array $fieldTemplates = [
        'default' => '{content}',
        'like' => '{content} LIKE',
        'in' => '{content} IN',
    ];

    public function findQueryFilterEqual(Query $query, array $options = []): Query
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('param tableField is necessary on options');
        }

        $conditions = $this->mapConditions($options['tableField'], $options['value'], $this->fieldTemplates['default']);

        return $query->where($conditions);
    }

    public function findQueryFilterSelect(Query $query, array $options = []): Query
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('param tableField is necessary on options');
        }

        $conditions = $this->mapConditions($options['tableField'], $options['value'], $this->fieldTemplates['default']);

        return $query->where($conditions);
    }

    public function findQueryFilterString(Query $query, array $options = []): Query
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('param tableField is necessary on options');
        }

        $valueTemplate = $options['template'] ?? QueryFilterPlugin::STRING_TEMPLATE_DEFAULT;
        $values = explode(' ', $options['value']);
        $conditions = [];
        
        foreach ($values as $value) {
            $formatValue = $this->formatTemplate($valueTemplate, ['content' => $value]);
            $conditions = array_merge($conditions, $this->mapConditions($options['tableField'], $formatValue, $this->fieldTemplates['like']));
        }

        return $query->where(['OR' => $conditions]);
    }

    protected function mapConditions($fields, $value, string $fieldTemplate = '{content}'): array
    {
        if (is_string($fields)) {
            $fields = [$fields];
        }

        $conditions = [];
        foreach ($fields as $field) {
            $fieldFormat = $this->formatTemplate($fieldTemplate, ['content' => $field]);
            $conditions[] = [$fieldFormat => $value];
        }

        return $conditions;
    }

    protected function formatTemplate(string $template, array $data, array $options = []): string
    {
        $options += ['before' => '{', 'after' => '}'];

        return Text::insert($template, $data, $options);
    }
}
