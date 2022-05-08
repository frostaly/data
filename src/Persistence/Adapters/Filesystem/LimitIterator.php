<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Adapters\Filesystem;

use Frostaly\Data\Persistence\Query;
use Iterator;

class LimitIterator extends \LimitIterator
{
    /**
     * Decorate the given iterator only if needed.
     */
    public static function decorate(Iterator $iterator, Query $query): Iterator
    {
        return isset($query->offset) || isset($query->limit)
            ? new self($iterator, $query->offset ?? 0, $query->limit ?? -1)
            : $iterator;
    }
}
