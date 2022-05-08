<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Persistence\Adapters;

use Frostaly\Data\Contracts\StoreAdapterInterface;
use Frostaly\Data\Persistence\Ordering;
use Frostaly\Data\Persistence\Predicates\Range;
use Frostaly\Data\Persistence\Query;
use Frostaly\Data\Tests\Resources\Resource;
use PHPUnit\Framework\TestCase;

abstract class AbstractStoreAdapterTest extends TestCase
{
    /**
     * The store adapter being tested.
     *
     * Must be setup with the given resources:
     *
     * - resource => Resource()
     * - first => Resource(sort: -1)
     * - last => Resource(sort: 1))
     * - unsorted => Resource()
     */
    protected StoreAdapterInterface $store;

    public function testAll(): void
    {
        $query = new Query($this->store);
        $this->assertEquals([
            'resource' => new Resource(uri: 'resource'),
            'first' => new Resource(uri: 'first', sort: -1),
            'last' => new Resource(uri: 'last', sort: 1),
            'unsorted' => new Resource(uri: 'unsorted'),
        ], iterator_to_array($this->store->all($query)));
    }

    public function testFind(): void
    {
        $resource = new Resource(uri: 'resource');
        $this->assertEquals($resource, $this->store->find('resource'));
        $this->assertNull($this->store->find('unknown'));
    }

    public function testAllWithPredicate(): void
    {
        $query = new Query($this->store, new Range('sort', -1, 1));
        $this->assertEquals([
            'first' => new Resource(uri: 'first', sort: -1),
            'last' => new Resource(uri: 'last', sort: 1),
        ], iterator_to_array($this->store->all($query)));
    }

    public function testAllWithOrderingAsc(): void
    {
        $query = new Query($this->store, orderings: ['sort' => Ordering::ASC]);
        $this->assertContains(
            array_keys(iterator_to_array($this->store->all($query))),
            [['resource', 'unsorted', 'first', 'last'], ['unsorted', 'resource', 'first', 'last']]
        );
    }

    public function testAllWithOrderingDesc(): void
    {
        $query = new Query($this->store, orderings: ['sort' => Ordering::DESC]);
        $this->assertContains(
            array_keys(iterator_to_array($this->store->all($query))),
            [['last', 'first', 'resource', 'unsorted'], ['last', 'first', 'unsorted', 'resource']]
        );
    }

    public function testAllWithOffset(): void
    {
        $query = new Query($this->store, offset: 1);
        $this->assertEquals(3, count(iterator_to_array($this->store->all($query))));
    }

    public function testAllWithLimit(): void
    {
        $query = new Query($this->store, limit: 1);
        $this->assertEquals(1, count(iterator_to_array($this->store->all($query))));
    }

    public function testPersistWithUri(): void
    {
        $resource = new Resource(uri: 'new');
        $this->assertNull($this->store->find('new'));
        $this->assertTrue($this->store->persist($resource));
        $this->assertEquals($resource, $this->store->find('new'));
    }

    public function testPersistWithoutUri(): void
    {
        $resource = new Resource();
        $this->assertNull($this->store->find('unknown'));
        $this->assertTrue($this->store->persist($resource));
        $this->assertNotNull($uri = $resource->uri);
        $this->assertEquals($resource, $this->store->find($uri));
    }

    public function testDeleteWithUri(): void
    {
        $resource = new Resource(uri: 'resource');
        $this->assertNotNull($this->store->find('resource'));
        $this->assertTrue($this->store->delete($resource));
        $this->assertNull($this->store->find('resource'));
    }

    public function testDeleteWithoutUri(): void
    {
        $resource = new Resource();
        $this->assertFalse($this->store->delete($resource));
    }
}
