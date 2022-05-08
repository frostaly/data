<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests;

use Frostaly\Data\Contracts\ResponderInterface;
use Frostaly\Data\ResponderLocator;
use Frostaly\Data\ResponderNegociator;
use Frostaly\Data\Responders\HtmlResponder;
use Frostaly\Data\Responders\JsonResponder;
use Frostaly\Data\Tests\Resources\Resource;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

class ResponderNegociatorTest extends TestCase
{
    private ResponderNegociator $responderNegociator;
    private ResponderLocator|Stub $responderLocator;
    private ServerRequestInterface $request;

    protected function setUp(): void
    {
        $this->request = (new Psr17Factory())->createServerRequest('GET', '/');
        $this->responderLocator = $this->createStub(ResponderLocator::class);
        $this->responderNegociator = new ResponderNegociator($this->responderLocator);
    }

    public function testPreferredResponder(): void
    {
        $this->responderLocator->method('get')->willReturnMap([
            [HtmlResponder::class, $htmlResponder = $this->createStub(HtmlResponder::class)],
            [JsonResponder::class, $jsonResponder = $this->createStub(JsonResponder::class)],
        ]);
        $this->assertEquals($htmlResponder, $this->responderNegociator->negociate(
            $this->request->withHeader('Accept', 'text/html,application/json;q=0.9'),
            new Resource(),
        ));
        $this->assertEquals($jsonResponder, $this->responderNegociator->negociate(
            $this->request->withHeader('Accept', 'text/html;q=0.9,application/json'),
            new Resource(),
        ));
    }

    public function testFallbackResponder(): void
    {
        $this->responderLocator->method('get')
            ->willReturn($responder = $this->createStub(ResponderInterface::class));
        $this->assertEquals($responder, $this->responderNegociator->negociate($this->request, new Resource()));
    }

    public function testResourceWithoutResponder(): void
    {
        $this->assertNull($this->responderNegociator->negociate($this->request, new class () extends Resource {
            public static array $responders = [];
        }));
    }
}
