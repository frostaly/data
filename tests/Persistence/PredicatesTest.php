<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Persistence;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;
use Frostaly\Data\Persistence\Predicates;
use Frostaly\Data\Tests\Resources\Resource;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class PredicatesTest extends TestCase
{
    public function testEqual(): void
    {
        $predicate = new Predicates\Equal('e', 'mc2');
        $this->assertTrue($predicate->check(new Resource(e: 'mc2')));
        $this->assertFalse($predicate->check(new Resource(e: '(╯°□°）╯︵ ┻━┻')));
    }

    public function testIn(): void
    {
        $predicate = new Predicates\In('scientists', ['Albert Einstein', 'Nikola Tesla']);
        $this->assertTrue($predicate->check(new Resource(scientists: 'Albert Einstein')));
        $this->assertTrue($predicate->check(new Resource(scientists: 'Nikola Tesla')));
        $this->assertFalse($predicate->check(new Resource(scientists: 'Arnold Schwarzenegger')));
    }

    public function testLike(): void
    {
        $predicate = new Predicates\Like('code', 'H____ %');
        $this->assertTrue($predicate->check(new Resource(code: 'Hello World')));
        $this->assertFalse($predicate->check(new Resource(code: 'NOT Hello World')));
        $this->assertFalse($predicate->check(new Resource(code: '¯\_(ツ)_/¯')));
    }

    public function testRegex(): void
    {
        $predicate = new Predicates\Regex('code', 'H.... [A-Za-z]+');
        $this->assertTrue($predicate->check(new Resource(code: 'Hello World')));
        $this->assertTrue($predicate->check(new Resource(code: 'SHOUT HELLO WOOORLD')));
        $this->assertFalse($predicate->check(new Resource(code: '¯\_(ツ)_/¯')));
    }

    public function testGt(): void
    {
        $predicate = new Predicates\Gt('value', 1);
        $this->assertTrue($predicate->check(new Resource(value: 2)));
        $this->assertFalse($predicate->check(new Resource(value: 1)));
        $this->assertFalse($predicate->check(new Resource(value: 0)));
    }

    public function testGte(): void
    {
        $predicate = new Predicates\Gte('value', 1);
        $this->assertTrue($predicate->check(new Resource(value: 2)));
        $this->assertTrue($predicate->check(new Resource(value: 1)));
        $this->assertFalse($predicate->check(new Resource(value: 0)));
    }

    public function testLt(): void
    {
        $predicate = new Predicates\Lt('value', 1);
        $this->assertTrue($predicate->check(new Resource(value: 0)));
        $this->assertFalse($predicate->check(new Resource(value: 1)));
        $this->assertFalse($predicate->check(new Resource(value: 2)));
    }

    public function testLte(): void
    {
        $predicate = new Predicates\Lte('value', 1);
        $this->assertTrue($predicate->check(new Resource(value: 0)));
        $this->assertTrue($predicate->check(new Resource(value: 1)));
        $this->assertFalse($predicate->check(new Resource(value: 2)));
    }

    public function testRange(): void
    {
        $predicate = new Predicates\Range('value', 1, 2);
        $this->assertFalse($predicate->check(new Resource(value: 0)));
        $this->assertTrue($predicate->check(new Resource(value: 1)));
        $this->assertTrue($predicate->check(new Resource(value: 2)));
        $this->assertFalse($predicate->check(new Resource(value: 3)));
    }

    public function testNil(): void
    {
        $predicate = new Predicates\Nil('void');
        $this->assertTrue($predicate->check(new Resource(void: null)));
        $this->assertFalse($predicate->check(new Resource(void: 'ಠ_ಠ')));
    }

    public function testNot(): void
    {
        $resource = $this->createMock(AbstractData::class);
        /** @var PredicateInterface|MockObject $passing */
        $passing = $this->createMock(PredicateInterface::class);
        /** @var PredicateInterface|MockObject $failing */
        $failing = $this->createMock(PredicateInterface::class);
        $passing->method('check')->with($resource)->willReturn(true);
        $failing->method('check')->with($resource)->willReturn(false);

        $this->assertFalse((new Predicates\Not($passing))->check($resource));
        $this->assertTrue((new Predicates\Not($failing))->check($resource));
    }

    public function testGroupAnd(): void
    {
        $resource = $this->createMock(AbstractData::class);
        /** @var PredicateInterface|MockObject $passing */
        $passing = $this->createMock(PredicateInterface::class);
        /** @var PredicateInterface|MockObject $failing */
        $failing = $this->createMock(PredicateInterface::class);
        $passing->method('check')->with($resource)->willReturn(true);
        $failing->method('check')->with($resource)->willReturn(false);

        $this->assertTrue((new Predicates\Group([$passing, $passing]))->check($resource));
        $this->assertFalse((new Predicates\Group([$passing, $failing]))->check($resource));
    }

    public function testGroupOr(): void
    {
        $resource = $this->createMock(AbstractData::class);
        /** @var PredicateInterface|MockObject $passing */
        $passing = $this->createMock(PredicateInterface::class);
        /** @var PredicateInterface|MockObject $failing */
        $failing = $this->createMock(PredicateInterface::class);
        $passing->method('check')->with($resource)->willReturn(true);
        $failing->method('check')->with($resource)->willReturn(false);

        $this->assertTrue((new Predicates\Group([$failing, $passing], false))->check($resource));
        $this->assertFalse((new Predicates\Group([$failing, $failing], false))->check($resource));
    }
}
