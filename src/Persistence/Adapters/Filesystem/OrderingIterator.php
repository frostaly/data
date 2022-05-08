<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Adapters\Filesystem;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Persistence\Ordering;
use Frostaly\Data\Persistence\Query;
use Iterator;

/**
 * @extends \ArrayIterator<string,AbstractData>
 */
class OrderingIterator extends \ArrayIterator
{
    /**
     * @param Iterator<string,AbstractData> $iterator
     * @param array<string,Ordering> $orderings
     */
    public function __construct(
        Iterator $iterator,
        private array $orderings,
    ) {
        parent::__construct(iterator_to_array($iterator));
        $this->uasort([$this, 'compare']);
    }

    /**
     * Uasort comparison function.
     */
    private function compare(AbstractData $a, AbstractData $b): int
    {
        foreach ($this->orderings as $field => $order) {
            $priority = match ($order) {
                Ordering::ASC => ($a->{$field} ?? -INF) <=> ($b->{$field} ?? -INF),
                Ordering::DESC => ($b->{$field} ?? -INF) <=> ($a->{$field} ?? -INF),
            };
            if ($priority !== 0) {
                return $priority;
            }
        }
        return 0;
    }

    /**
     * Decorate the given iterator only if needed.
     */
    public static function decorate(Iterator $iterator, Query $query): Iterator
    {
        return isset($query->orderings)
            ? new self($iterator, $query->orderings)
            : $iterator;
    }
}
