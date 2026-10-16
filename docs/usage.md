# QueryFilter — Usage

## Registering filter fields

The behavior maps an input key to a finder. Register fields either through the
behavior config or with `addFilterField()`:

```php
// config
$this->addBehavior('QueryFilter.QueryFilter', [
    'filterFields' => [
        'name' => ['tableField' => 'name', 'finder' => QueryFilter::FINDER_STRING],
    ],
]);

// or programmatically
$this->addFilterField('status', [
    'tableField' => 'status',
    'finder' => QueryFilter::FINDER_SELECT,
]);
```

Each field accepts:

| Option | Required | Description |
|--------|----------|-------------|
| `finder` | yes | A finder name (see [finders](finders.md)) or a callable. |
| `tableField` | usually | A column name or a list of columns. |
| `templateField` | no | Field template override (see [templates](templates.md)). |
| `templateValue` | no | Value template override. |

When `finder` is a string it must be a finder known to the table (validate with
`addFilterField()`, which throws otherwise). Callables receive
`($query, $options)` and must return the query:

```php
$this->addFilterField('lapse', [
    'finder' => function (Query $query, array $options): Query {
        return $query->where(['lapse_id IN' => $options['value']]);
    },
]);
```

## Applying filters

```php
$query = $this->Students->find();
$query = $this->Students->queryFilter($query, $this->request->getQuery('f') ?? []);
```

The second argument is the filter payload (`input key => value`). Empty values
(`null`, `''`, `[]`) are ignored; `0` and `'0'` are **kept**, so boolean/select
filters like `active=0` work.

### Inspecting the result

```php
$this->Students->queryWasFiltered();   // bool: at least one filter applied
$this->Students->queryFiltered();      // int: number of filters applied
$this->Students->appliedFilters();     // array: input key => value actually applied
```

## Form / request shape

The convention is a single query key (default `f`) holding the filter fields:

```
/students/index?f[name]=ada&f[status]=active
```

```php
// template
echo $this->Form->create(null, ['type' => 'get']);
echo $this->Form->control('f.name');
echo $this->Form->control('f.status');
echo $this->Form->submit();
echo $this->Form->end();
```

See [events](events.md) to hook into filtering (logging, recent filters, …).

----------
[Back to index](index.md)
