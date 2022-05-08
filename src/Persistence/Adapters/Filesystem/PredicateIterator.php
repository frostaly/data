<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Adapters\Filesystem;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;
use Frostaly\Data\Persistence\Query;
use Iterator;

/**
 * @extends \FilterIterator<string,AbstractData,Iterator>
 */
class PredicateIterator extends \FilterIterator
{
    /**
     * @param Iterator<string,AbstractData> $iterator
     */
    public function __construct(
        Iterator $iterator,
        private PredicateInterface $predicate,
    ) {
        parent::__construct($iterator);
    }

    /**
     * {@inheritdoc}
     */
    public function accept(): bool
    {
        return $this->predicate->check($this->current());
    }

    /**
     * Decorate the given iterator only if needed.
     */
    public static function decorate(Iterator $iterator, Query $query): Iterator
    {
        return isset($query->predicate)
            ? new self($iterator, $query->predicate)
            : $iterator;
    }
}
