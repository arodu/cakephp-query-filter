<?php
declare(strict_types=1);

namespace QueryFilter\Model\Traits;

use Cake\Utility\Text;
use InvalidArgumentException;
use QueryFilter\QueryFilter;

/**
 * Shared helpers used by the finder traits to build query conditions.
 */
trait QueryFilterConditionsTrait
{
    /**
     * Interpolates `{content}` style placeholders in a template.
     *
     * @param string $template The template, e.g. `{content} LIKE`.
     * @param array $data The placeholder data.
     * @param array $options Interpolation options (`before`/`after` delimiters).
     * @return string
     */
    protected function formatTemplate(string $template, array $data, array $options = []): string
    {
        $options += ['before' => '{', 'after' => '}'];

        return Text::insert($template, $data, $options);
    }

    /**
     * Returns the required `tableField` option (a field name or a list of fields).
     *
     * @param array $options The finder options.
     * @return array|string
     * @throws \InvalidArgumentException When the option is missing.
     */
    protected function requireTableField(array $options): string|array
    {
        if (empty($options['tableField'])) {
            throw new InvalidArgumentException('The "tableField" option is required.');
        }

        return $options['tableField'];
    }

    /**
     * Returns the required `value` option, preserving falsy values such as `0`.
     *
     * @param array $options The finder options.
     * @return mixed
     * @throws \InvalidArgumentException When the option is missing.
     */
    protected function requireValue(array $options): mixed
    {
        if (!array_key_exists('value', $options)) {
            throw new InvalidArgumentException('The "value" option is required.');
        }

        return $options['value'];
    }

    /**
     * Builds `[field => value]` pairs for every value × field combination.
     *
     * Used by search finders where any field/value pair may match (OR).
     *
     * @param array|string $fields The field(s).
     * @param array $values The value(s).
     * @param array $options Template options (`templateField`, `templateValue`).
     * @return array
     */
    protected function mapConditions(string|array $fields, array $values, array $options = []): array
    {
        $templateField = $options['templateField'] ?? QueryFilter::FIELD_TEMPLATE_DEFAULT;
        $templateValue = $options['templateValue'] ?? QueryFilter::STRING_TEMPLATE_DEFAULT;

        $conditions = [];
        foreach ((array)$fields as $field) {
            $formattedField = $this->formatTemplate($templateField, ['content' => $field]);
            foreach ($values as $value) {
                $conditions[] = [
                    $formattedField => $this->formatTemplate($templateValue, ['content' => (string)$value]),
                ];
            }
        }

        return $conditions;
    }

    /**
     * Builds `[field OP => value]` pairs, one per field.
     *
     * The value is kept as-is, so array values (e.g. for `IN`) work correctly.
     *
     * @param array|string $fields The field(s).
     * @param mixed $value The value.
     * @param array $options Template options (`templateField`).
     * @return array
     */
    protected function mapFieldConditions(string|array $fields, mixed $value, array $options = []): array
    {
        $templateField = $options['templateField'] ?? QueryFilter::FIELD_TEMPLATE_DEFAULT;

        $conditions = [];
        foreach ((array)$fields as $field) {
            $formattedField = $this->formatTemplate($templateField, ['content' => $field]);
            $conditions[$formattedField] = $value;
        }

        return $conditions;
    }
}
