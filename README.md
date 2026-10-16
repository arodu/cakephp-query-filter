# QueryFilter plugin for CakePHP

Declarative query filtering for CakePHP index actions. Map form input keys to
table finders and apply them to a query in a single call, keeping controllers
free of filter plumbing.

## Requirements

- PHP >= 8.2
- `cakephp/cakephp` ^5.0

## Installation

```
composer require arodu/cakephp-query-filter:^3.0
```

Load the plugin in `src/Application.php`:

```php
$this->addPlugin(\QueryFilter\QueryFilterPlugin::class);
```

## Quick start

```php
// In the table's initialize() (or a setup method)
$this->addBehavior('QueryFilter.QueryFilter', [
    'filterFields' => [
        'name' => ['tableField' => 'name', 'finder' => \QueryFilter\QueryFilter::FINDER_STRING],
        'status' => ['tableField' => 'status', 'finder' => \QueryFilter\QueryFilter::FINDER_SELECT],
    ],
]);

// In the controller action
$query = $this->Students->find();
$query = $this->Students->queryFilter($query, $this->request->getQuery('f') ?? []);
```

## Documentation

- [Usage](docs/usage.md) — registering filters and applying them.
- [Finders reference](docs/finders.md) — every finder, its options and examples.
- [Templates](docs/templates.md) — building field/value conditions.
- [Events](docs/events.md) — `beforeFilter`, `afterFilter`, `beforeFinder`, `afterFinder`.

## License

MIT
