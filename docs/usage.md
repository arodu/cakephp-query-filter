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

`finder` is usually one of the built-ins (see [finders](finders.md)). For
anything the built-ins don't cover, use a [custom finder](#custom-finders).

## Custom finders

For special cases a filter field can use a custom finder: either an **inline
callable** or a **finder defined on the Table** and referenced by name.

### Inline callable

```php
$this->addFilterField('lapse', [
    'finder' => function (Query $query, array $options): Query {
        return $query->where(['lapse_id IN' => $options['value']]);
    },
]);
```

### A finder on the Table class

Define a regular table finder and reference it by name. `addFilterField()`
checks that the finder exists, so typos fail fast.

```php
// src/Model/Table/StudentsTable.php
public function findByNameOrDni(Query $query, array $options = []): Query
{
    return $query->where([
        'OR' => [
            'name LIKE' => '%' . $options['value'] . '%',
            'dni' => $options['value'],
        ],
    ]);
}
```

```php
// register it (the method `findByNameOrDni` is referenced as `nameOrDni`)
$this->addFilterField('q', [
    'finder' => 'nameOrDni',
]);
```

### Contract

Every custom finder receives `($query, $options)` and **must return the query**.
`$options` always contains the submitted `value` and the input `key`, plus any
extra options declared on the filter field (e.g. `tableField`). Callables run
as-is; string finders are resolved against the table.

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
