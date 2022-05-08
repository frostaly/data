<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Predicates;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;

class In implements PredicateInterface
{
    public function __construct(
        public string $field,
        public array $values,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function check(AbstractData $resource): bool
    {
        return isset($resource->{$this->field})
            ? in_array($resource->{$this->field}, $this->values)
            : false;
    }
}
