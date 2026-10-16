# QueryFilter — Finders reference

All finders live under the `QueryFilter\QueryFilter` constants and can be used
directly (`$table->find(QueryFilter::FINDER_STRING, $options)`) or through a
registered filter field.

| Constant | Find name | Options | Produces |
|----------|-----------|---------|----------|
| `FINDER_EQUAL` | `queryFilterEqual` | `tableField`, `value` | `field = value` (AND across fields) |
| `FINDER_NOT_EQUAL` | `queryFilterNotEqual` | `tableField`, `value` | `field != value` (AND across fields) |
| `FINDER_IN` | `queryFilterIn` | `tableField`, `value` (array) | `field IN (…)` (OR across fields) |
| `FINDER_NOT_IN` | `queryFilterNotIn` | `tableField`, `value` (array) | `field NOT IN (…)` (AND across fields) |
| `FINDER_SELECT` | `queryFilterSelect` | `tableField`, `value` | `=` for a scalar, `IN` for an array |
| `FINDER_MULTI_SELECT` | `queryFilterMultiSelect` | `tableField`, `value` (array) | `field IN (…)` |
| `FINDER_STRING` | `queryFilterString` | `tableField`, `value` | tokenized `LIKE '%token%'` (OR) |
| `FINDER_GREATER_THAN` | `queryFilterGreaterThan` | `tableField`, `value` | `field > value` |
| `FINDER_GREATER_THAN_OR_EQUAL` | `queryFilterGreaterThanOrEqual` | `tableField`, `value` | `field >= value` |
| `FINDER_LESS_THAN` | `queryFilterLessThan` | `tableField`, `value` | `field < value` |
| `FINDER_LESS_THAN_OR_EQUAL` | `queryFilterLessThanOrEqual` | `tableField`, `value` | `field <= value` |
| `FINDER_IS_TODAY` | `queryFilterIsToday` | `tableField` | day range for today |
| `FINDER_IS_TOMORROW` | `queryFilterIsTomorrow` | `tableField` | day range for tomorrow |
| `FINDER_IS_YESTERDAY` | `queryFilterIsYesterday` | `tableField` | day range for yesterday |
| `FINDER_IS_PAST` | `queryFilterIsPast` | `tableField` | `field < now` |
| `FINDER_IS_FUTURE` | `queryFilterIsFuture` | `tableField` | `field > now` |

`tableField` may be a single column or an array of columns.

## Equality and sets

```php
// active records
$table->find(QueryFilter::FINDER_EQUAL, ['tableField' => 'status', 'value' => 'active']);

// any of several statuses
$table->find(QueryFilter::FINDER_IN, ['tableField' => 'status', 'value' => ['active', 'pending']]);

// everything except inactive
$table->find(QueryFilter::FINDER_NOT_IN, ['tableField' => 'status', 'value' => ['inactive']]);
```

## Text search

Splits the value on spaces and matches rows where any column contains any token.
Quoted segments stay together and the quotes are stripped:

```php
$table->find(QueryFilter::FINDER_STRING, ['tableField' => ['first_name', 'last_name'], 'value' => 'ada lovelace']);
$table->find(QueryFilter::FINDER_STRING, ['tableField' => 'name', 'value' => '"grace hopper"']);
```

## Comparison

```php
$table->find(QueryFilter::FINDER_GREATER_THAN_OR_EQUAL, ['tableField' => 'age', 'value' => 18]);
$table->find(QueryFilter::FINDER_LESS_THAN, ['tableField' => 'created', 'value' => $limit]);
```

`value` must be a scalar or a `DateTimeInterface` (arrays are rejected).

## Dates

`IsToday` / `IsTomorrow` / `IsYesterday` match the whole day; `IsPast` /
`IsFuture` compare against now.

```php
$table->find(QueryFilter::FINDER_IS_TODAY, ['tableField' => 'created']);
$table->find(QueryFilter::FINDER_IS_FUTURE, ['tableField' => 'due_date']);
```

----------
[Back to index](index.md)
