<?php

declare(strict_types=1);

namespace Frostaly\Data\Contracts;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Persistence\Query;

interface StoreAdapterInterface
{
    /**
     * Find resources matching the given query.
     */
    public function all(Query $query): iterable;

    /**
     * Find the resource with the given uri.
     */
    public function find(int|string $uri): ?AbstractData;

    /**
     * Persist the given resource.
     */
    public function persist(AbstractData $resource): bool;

    /**
     * Delete the given resource.
     */
    public function delete(AbstractData $resource): bool;
}
