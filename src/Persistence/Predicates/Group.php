<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Predicates;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;

class Group implements PredicateInterface
{
    /**
     * @param PredicateInterface[] $predicates
     */
    public function __construct(
        public array $predicates,
        public bool $checkAll = true,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function check(AbstractData $resource): bool
    {
        if ($this->checkAll) {
            return $this->checkAll($resource);
        }
        return $this->checkOne($resource);
    }

    /**
     * Check that all predicates are valid.
     */
    private function checkAll(AbstractData $resource): bool
    {
        foreach ($this->predicates as $predicate) {
            if (!$predicate->check($resource)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check that only one predicate is valid.
     */
    private function checkOne(AbstractData $resource): bool
    {
        foreach ($this->predicates as $predicate) {
            if ($predicate->check($resource)) {
                return true;
            }
        }
        return false;
    }
}
