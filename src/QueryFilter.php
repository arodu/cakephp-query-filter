<?php
declare(strict_types=1);

namespace QueryFilter;

/**
 * QueryFilter public constants.
 *
 * Events, finder names and templates live here so the plugin class stays a
 * thin lifecycle shell (mirrors the `CakeLte` / `CakeLtePlugin` split).
 */
abstract class QueryFilter
{
    /**
     * Dispatched before the filters are applied (whole operation).
     *
     * Data: `table`, `alias`, `data` (raw input), `key`, `request`.
     */
    public const EVENT_BEFORE_FILTER = 'QueryFilter.beforeFilter';

    /**
     * Dispatched after the filters have been applied (whole operation).
     *
     * Data: `table`, `alias`, `data` (applied filters), `count`, `key`, `request`.
     * Useful to persist recent filters or log/measure filtering.
     */
    public const EVENT_AFTER_FILTER = 'QueryFilter.afterFilter';

    /**
     * Dispatched right before a single field finder is applied.
     *
     * Data: `table`, `alias`, `key`, `value`, `finder`, `options`.
     */
    public const EVENT_BEFORE_FINDER = 'QueryFilter.beforeFinder';

    /**
     * Dispatched right after a single field finder has been applied.
     *
     * Data: `table`, `alias`, `key`, `value`, `finder`, `query`.
     */
    public const EVENT_AFTER_FINDER = 'QueryFilter.afterFinder';

    /**
     * Default key under which the filter payload is read from request data.
     */
    public const DEFAULT_FILTER_KEY = 'f';

    /**
     * Equality finders.
     */
    public const FINDER_EQUAL = 'queryFilterEqual';
    public const FINDER_NOT_EQUAL = 'queryFilterNotEqual';

    /**
     * Set finders.
     */
    public const FINDER_IN = 'queryFilterIn';
    public const FINDER_NOT_IN = 'queryFilterNotIn';
    public const FINDER_SELECT = 'queryFilterSelect';
    public const FINDER_MULTI_SELECT = 'queryFilterMultiSelect';

    /**
     * Text finder.
     */
    public const FINDER_STRING = 'queryFilterString';

    /**
     * Comparison finders.
     */
    public const FINDER_GREATER_THAN = 'queryFilterGreaterThan';
    public const FINDER_GREATER_THAN_OR_EQUAL = 'queryFilterGreaterThanOrEqual';
    public const FINDER_LESS_THAN = 'queryFilterLessThan';
    public const FINDER_LESS_THAN_OR_EQUAL = 'queryFilterLessThanOrEqual';

    /**
     * Date finders.
     */
    public const FINDER_IS_TODAY = 'queryFilterIsToday';
    public const FINDER_IS_TOMORROW = 'queryFilterIsTomorrow';
    public const FINDER_IS_YESTERDAY = 'queryFilterIsYesterday';
    public const FINDER_IS_PAST = 'queryFilterIsPast';
    public const FINDER_IS_FUTURE = 'queryFilterIsFuture';

    /**
     * Value templates.
     */
    public const STRING_TEMPLATE_DEFAULT = '{content}';
    public const STRING_TEMPLATE_LIKE_INNER = '%{content}%';
    public const STRING_TEMPLATE_LIKE_BEFORE = '{content}%';
    public const STRING_TEMPLATE_LIKE_AFTER = '%{content}';

    /**
     * Field templates.
     */
    public const FIELD_TEMPLATE_DEFAULT = '{content}';
    public const FIELD_TEMPLATE_LIKE = '{content} LIKE';
    public const FIELD_TEMPLATE_IN = '{content} IN';
    public const FIELD_TEMPLATE_NOT_IN = '{content} NOT IN';
    public const FIELD_TEMPLATE_NOT_EQUAL = '{content} !=';
    public const FIELD_TEMPLATE_GREATER_THAN = '{content} >';
    public const FIELD_TEMPLATE_GREATER_THAN_OR_EQUAL = '{content} >=';
    public const FIELD_TEMPLATE_LESS_THAN = '{content} <';
    public const FIELD_TEMPLATE_LESS_THAN_OR_EQUAL = '{content} <=';
}
