<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Predicates;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;

class Nil implements PredicateInterface
{
    public function __construct(
        public string $field,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function check(AbstractData $resource): bool
    {
        return !isset($resource->{$this->field});
    }
}
