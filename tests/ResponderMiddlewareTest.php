<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests;

use Frostaly\Data\Contracts\ResponderInterface;
use Frostaly\Data\Persistence\Store;
use Frostaly\Data\ResponderMiddleware;
use Frostaly\Data\ResponderNegociator;
use Frostaly\Data\Tests\Resources\Resource;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;

class ResponderMiddlewareTest extends TestCase
{
    private Store|MockObject $store;
    private ResponderNegociator|MockObject $negociator;
    private RequestHandlerInterface|MockObject $requestHandler;
    private ResponderInterface|Stub $responder;
    private ResponderMiddleware $middleware;
    private Psr17Factory $psrFactory;

    protected function setUp(): void
    {
        $this->store = $this->createMock(Store::class);
        $this->negociator = $this->createMock(ResponderNegociator::class);
        $this->requestHandler = $this->createMock(RequestHandlerInterface::class);
        $this->responder = $this->createStub(ResponderInterface::class);
        $this->middleware = new ResponderMiddleware($this->store, $this->negociator);
        $this->psrFactory = new Psr17Factory();
    }

    public function testHandleGetMethod(): void
    {
        $request = $this->psrFactory->createServerRequest('GET', 'article');
        $this->store->expects($this->once())->method('find')->with('article')
            ->willReturn($resource = new Resource(ping: 'pong'));
        $this->negociator->expects($this->once())->method('negociate')->with($request, $resource)
            ->willReturn($this->responder);
        $this->responder->method('respond')
            ->willReturn($response = $this->psrFactory->createResponse());
        $this->requestHandler->expects($this->never())->method('handle');
        $this->assertEquals($response, $this->middleware->process($request, $this->requestHandler));
    }

    public function testHandleOtherMethod(): void
    {
        $request = $this->psrFactory->createServerRequest('POST', 'article');
        $this->store->expects($this->never())->method('find');
        $this->negociator->expects($this->never())->method('negociate');
        $this->requestHandler->expects($this->once())->method('handle');
        $this->middleware->process($request, $this->requestHandler);
    }

    public function testResourceNotFound(): void
    {
        $request = $this->psrFactory->createServerRequest('GET', 'article');
        $this->store->expects($this->once())->method('find')->with('article');
        $this->negociator->expects($this->never())->method('negociate');
        $this->requestHandler->expects($this->once())->method('handle');
        $this->middleware->process($request, $this->requestHandler);
    }
}
