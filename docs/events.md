# QueryFilter — Events

The behavior dispatches four events on the table's event manager. Listen to
them to log, measure, persist recent filters, etc.

| Constant | Name | When |
|----------|------|------|
| `QueryFilter::EVENT_BEFORE_FILTER` | `QueryFilter.beforeFilter` | Before applying the filters. |
| `QueryFilter::EVENT_AFTER_FILTER` | `QueryFilter.afterFilter` | After applying the filters. |
| `QueryFilter::EVENT_BEFORE_FINDER` | `QueryFilter.beforeFinder` | Before applying a single field finder. |
| `QueryFilter::EVENT_AFTER_FINDER` | `QueryFilter.afterFinder` | After applying a single field finder. |

## Payloads

Common data: `table`, `alias`.

- `beforeFilter`: `data` (raw payload), `key`, `request`.
- `afterFilter`: `data` (applied filters), `count`, `key`, `request`.
- `beforeFinder`: `key`, `value`, `finder`, `options`.
- `afterFinder`: `key`, `value`, `finder`, `query`.

`key` is the group key. Pass it when applying filters:

```php
$behavior = $this->Students->getBehavior('QueryFilter');
$query = $behavior->queryFilter($query, $filters, ['key' => 'students.index']);
```

## Example — recent filters

If the application has a `RecentFilters` model, save the filter that was used:

```php
use Cake\Event\Event;
use Cake\ORM\TableRegistry;
use QueryFilter\QueryFilter;

// e.g. in Application::bootstrap() or a controller
$this->Students->getEventManager()->on(
    QueryFilter::EVENT_AFTER_FILTER,
    function (Event $event): void {
        $data = $event->getData();
        if ($data['count'] === 0) {
            return;
        }

        $locator = TableRegistry::getTableLocator();
        if (!$locator->has('RecentFilters')) {
            return;
        }

        $recent = $locator->get('RecentFilters')->newEmptyEntity();
        $recent->group_key = $data['key'];
        $recent->filter = json_encode($data['data']);
        $locator->get('RecentFilters')->save($recent);
    },
);
```

The plugin itself stores nothing; the table/model lives in the application. See
the `afterFilter` payload to build "recent filters" or saved-filter features.

----------
[Back to index](index.md)
