<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;
use Frostaly\Data\Contracts\StoreAdapterInterface;
use Frostaly\Data\Persistence\Ordering;
use Frostaly\Data\Persistence\Predicates;

final class Query
{
    public function __construct(
        private StoreAdapterInterface $adapter,
        public readonly ?PredicateInterface $predicate = null,
        public readonly ?array $orderings = null,
        public readonly ?int $offset = null,
        public readonly ?int $limit = null,
    ) {}

    /**
     * Execute the query and get all resources.
     */
    public function all(bool $asArray = false): iterable
    {
        $resources = $this->adapter->all($this);
        return $asArray ? [...$resources] : $resources;
    }

    /**
     * Execute the query and get the first resource.
     */
    public function first(): ?AbstractData
    {
        foreach ($this->limit(1)->all(false) as $resource) {
            return $resource;
        }
        return null;
    }

    /**
     * Return an instance with the where clause.
     */
    public function where(PredicateInterface ...$predicates): static
    {
        return match (count($predicates)) {
            0 => $this->with(predicate: null),
            1 => $this->with(predicate: reset($predicates)),
            default => $this->with(predicate: new Predicates\Group($predicates)),
        };
    }

    /**
     * Return an instance with the ordering clause.
     *
     * @param array<string,Ordering> $orderings
     */
    public function orderBy(array $orderings): static
    {
        return $this->with(orderings: $orderings);
    }

    /**
     * Return an instance with the amount of resources to skip.
     */
    public function offset(int $number): static
    {
        return $this->with(offset: $number);
    }

    /**
     * Return an instance with the amount of resources to keep.
     */
    public function limit(int $number): static
    {
        return $this->with(limit: $number);
    }

    /**
     * Return an instance with the new property.
     */
    private function with(mixed ...$properties): static
    {
        return new static(
            $this->adapter,
            /** @phpstan-ignore-next-line */
            ...array_merge(array_filter(
                (array) $this,
                fn($key) => $key[0] !== "\0",
                ARRAY_FILTER_USE_KEY,
            ), $properties),
        );
    }
}
