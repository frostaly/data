<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests;

use Frostaly\Data\Contracts\ResponderInterface;
use Frostaly\Data\ResponderLocator;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use RuntimeException;

class ResponderLocatorTest extends TestCase
{
    private ContainerInterface|Stub $container;
    private ResponderLocator $responderLocator;

    protected function setUp(): void
    {
        $this->container = $this->createStub(ContainerInterface::class);
        $this->responderLocator = new ResponderLocator($this->container);
    }

    public function testGetValidResponder(): void
    {
        $this->container->method('get')->willReturn($responder = $this->createStub(ResponderInterface::class));
        $this->assertSame($responder, $this->responderLocator->get(ResponderInterface::class));
    }

    public function testGetInvalidResponder(): void
    {
        $this->expectException(RuntimeException::class);
        $this->responderLocator->get('¯\_(ツ)_/¯');
    }
}
