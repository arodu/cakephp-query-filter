<?php

declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\ORM\Query;
use Cake\Utility\Text;
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
        $conditions = $this->mapConditions($options['tableField'], $options['value'], $this->fieldTemplates['default']);

        return $query->where($conditions);
    }

    public function findQueryFilterSelect(Query $query, array $options = []): Query
    {
        $conditions = $this->mapConditions($options['tableField'], $options['value'], $this->fieldTemplates['default']);

        return $query->where($conditions);
    }

    public function findQueryFilterString(Query $query, array $options = []): Query
    {
        $valueTemplate = $options['template'] ?? QueryFilterPlugin::STRING_TEMPLATE_DEFAULT;
        $value = $this->formatTemplate($valueTemplate, ['content' => $options['value']]);
        $conditions = $this->mapConditions($options['tableField'], $value, $this->fieldTemplates['like']);

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
            $conditions[$fieldFormat] = $value;
        }

        return $conditions;
    }

    protected function formatTemplate(string $template, array $data, array $options = []): string
    {
        $options += ['before' => '{', 'after' => '}'];

        return Text::insert($template, $data, $options);
    }
}
