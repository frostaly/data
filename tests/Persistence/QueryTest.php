<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Persistence;

use Frostaly\Data\Contracts\PredicateInterface;
use Frostaly\Data\Contracts\StoreAdapterInterface;
use Frostaly\Data\Persistence\Ordering;
use Frostaly\Data\Persistence\Predicates\Group;
use Frostaly\Data\Persistence\Query;
use Frostaly\Data\Tests\Resources\Resource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class QueryTest extends TestCase
{
    private Query $query;
    private StoreAdapterInterface|MockObject $adapter;

    protected function setUp(): void
    {
        $this->adapter = $this->createStub(StoreAdapterInterface::class);
        $this->query = new Query($this->adapter);
    }

    public function testAll(): void
    {
        $this->adapter->method('all')
            ->with($this->query)
            ->willReturn($resources = [
                new Resource(windows: 'Bill Gates'),
                new Resource(linux: 'Linus Torvalds'),
            ]);
        $this->assertEquals($resources, $this->query->all(true));
    }

    public function testFirst(): void
    {
        $this->adapter->method('all')
            ->with($this->query->limit(1))
            ->willReturn([], $resources = [
                new Resource('Ada Lovelace'),
                new Resource('Margaret Hamilton'),
            ]);
        $this->assertNull($this->query->first());
        $this->assertEquals($resources[0], $this->query->first());
    }

    public function testWhere(): void
    {
        $predicate = $this->createStub(PredicateInterface::class);
        $query = $this->query->where($predicate);
        $this->assertEquals($predicate, $query->predicate);
        $query = $this->query->where($predicate, $this->createStub(PredicateInterface::class));
        $this->assertInstanceOf(Group::class, $query->predicate);
    }

    public function testOrderBy(): void
    {
        $query = $this->query->orderBy($orderings = [
            'up' => Ordering::ASC,
            'down' => Ordering::DESC,
        ]);
        $this->assertEquals($orderings, $query->orderings);
    }

    public function testOffset(): void
    {
        $query = $this->query->offset(1685);
        $this->assertEquals(1685, $query->offset);
    }

    public function testLimit(): void
    {
        $query = $this->query->limit(1750);
        $this->assertEquals(1750, $query->limit);
    }
}
