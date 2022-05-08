<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Persistence;

use Frostaly\Data\Contracts\StoreAdapterInterface;
use Frostaly\Data\Persistence\Query;
use Frostaly\Data\Persistence\Store;
use Frostaly\Data\Tests\Resources\Resource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class StoreTest extends TestCase
{
    private Store $store;
    private StoreAdapterInterface|MockObject $adapter;

    protected function setUp(): void
    {
        $this->adapter = $this->createMock(StoreAdapterInterface::class);
        $this->store = new Store($this->adapter);
    }

    public function testFind(): void
    {
        $this->adapter->expects($this->once())->method('find')->with('truth');
        $this->store->find('truth');
    }

    public function testPersist(): void
    {
        $resource = new Resource(hero: 'Alan Turing');
        $this->adapter->expects($this->once())->method('persist')->with($resource);
        $this->store->persist($resource);
    }

    public function testDelete(): void
    {
        $resource = new Resource(villain: 'Thomas Midgley');
        $this->adapter->expects($this->once())->method('delete')->with($resource);
        $this->store->delete($resource);
    }

    public function testValidQueryCall(): void
    {
        $query = $this->store->where();
        $this->assertInstanceOf(Query::class, $query);
    }

    public function testInvalidQueryCall(): void
    {
        $this->expectException(\BadMethodCallException::class);
        $this->store->{'¯\_(ツ)_/¯'}();
    }
}
