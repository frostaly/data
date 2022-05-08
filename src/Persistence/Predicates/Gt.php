<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Predicates;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;

class Gt implements PredicateInterface
{
    public function __construct(
        public string $field,
        public int|float|string $value,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function check(AbstractData $resource): bool
    {
        return isset($resource->{$this->field})
            ? $resource->{$this->field} > $this->value
            : false;
    }
}
