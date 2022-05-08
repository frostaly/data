<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\StoreAdapterInterface;

class Store
{
    public function __construct(
        private StoreAdapterInterface $adapter,
    ) {}

    /**
     * Find the resource with the given uri.
     */
    public function find(int|string $uri): ?AbstractData
    {
        return $this->adapter->find($uri);
    }

    /**
     * Persist the given resource.
     */
    public function persist(AbstractData $resource): void
    {
        $this->adapter->persist($resource);
    }

    /**
     * Delete the given resource.
     */
    public function delete(AbstractData $resource): void
    {
        $this->adapter->delete($resource);
    }

    /**
     * Proxy calls to the query object.
     */
    public function __call(string $method, array $arguments): mixed
    {
        if (method_exists(Query::class, $method)) {
            return (new Query($this->adapter))->{$method}(...$arguments);
        }
        throw new \BadMethodCallException(
            sprintf('Method %s does not exist', __METHOD__),
        );
    }
}
