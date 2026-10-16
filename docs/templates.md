# QueryFilter — Templates

Finders build conditions by interpolating two templates: one for the **field**
(the condition key, where comparison operators are embedded) and one for the
**value**. Both use `{content}` as the placeholder.

## Value templates (`QueryFilter::STRING_TEMPLATE_*`)

| Constant | Value |
|----------|-------|
| `STRING_TEMPLATE_DEFAULT` | `{content}` |
| `STRING_TEMPLATE_LIKE_INNER` | `%{content}%` |
| `STRING_TEMPLATE_LIKE_BEFORE` | `{content}%` |
| `STRING_TEMPLATE_LIKE_AFTER` | `%{content}` |

## Field templates (`QueryFilter::FIELD_TEMPLATE_*`)

| Constant | Value |
|----------|-------|
| `FIELD_TEMPLATE_DEFAULT` | `{content}` |
| `FIELD_TEMPLATE_LIKE` | `{content} LIKE` |
| `FIELD_TEMPLATE_IN` | `{content} IN` |
| `FIELD_TEMPLATE_NOT_IN` | `{content} NOT IN` |
| `FIELD_TEMPLATE_NOT_EQUAL` | `{content} !=` |
| `FIELD_TEMPLATE_GREATER_THAN` | `{content} >` |
| `FIELD_TEMPLATE_GREATER_THAN_OR_EQUAL` | `{content} >=` |
| `FIELD_TEMPLATE_LESS_THAN` | `{content} <` |
| `FIELD_TEMPLATE_LESS_THAN_OR_EQUAL` | `{content} <=` |

For example, `FIELD_TEMPLATE_LIKE` with column `name` and
`STRING_TEMPLATE_LIKE_INNER` with value `ada` produce `['name LIKE' => '%ada%']`.

## Overriding templates

Pass `templateField` / `templateValue` in the finder options (or the filter
field) to change the behaviour:

```php
$table->find(QueryFilter::FINDER_STRING, [
    'tableField' => 'name',
    'value' => 'ada',
    'templateValue' => QueryFilter::STRING_TEMPLATE_LIKE_AFTER, // 'ada%'
]);
```

----------
[Back to index](index.md)
