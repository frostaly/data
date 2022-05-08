<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Predicates;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;

class Regex implements PredicateInterface
{
    public function __construct(
        public string $field,
        public string $pattern,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function check(AbstractData $resource): bool
    {
        return isset($resource->{$this->field})
            ? (bool) preg_match('/' . $this->pattern . '/u', (string) $resource->{$this->field})
            : false;
    }
}
