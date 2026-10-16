<?php
declare(strict_types=1);

namespace QueryFilter\Model\Traits;

/**
 * Aggregates every QueryFilter finder trait.
 *
 * Include this single trait to expose all finders (this is what the behavior
 * does), or include the individual traits when only a subset is needed.
 */
trait QueryFilterAllFindersTrait
{
    use QueryFilterFindersTrait;
    use QueryFilterTextFindersTrait;
    use QueryFilterComparisonFindersTrait;
    use QueryFilterDatesFindersTrait;
}
