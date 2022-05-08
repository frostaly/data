<?php

declare(strict_types=1);

namespace Frostaly\Data\Contracts;

use Frostaly\Data\AbstractData;

interface PredicateInterface
{
    /**
     * Check whether the given resource satisfy the condition.
     */
    public function check(AbstractData $resource): bool;
}
