<?php

declare(strict_types=1);

namespace Frostaly\Data;

use Frostaly\Data\Persistence\Store;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ResponderMiddleware implements MiddlewareInterface
{
    public function __construct(
        private Store $store,
        private ResponderNegociator $negociator,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (strtoupper($request->getMethod()) === 'GET') {
            $response = $this->handleRequest($request);
        }
        return $response ?? $handler->handle($request);
    }

    /**
     * Handles the given request and produces a response.
     */
    private function handleRequest(ServerRequestInterface $request): ?ResponseInterface
    {
        $uri = $request->getUri()->getPath();
        $resource = $this->store->find($uri);
        return $resource ? $this->negociator
            ->negociate($request, $resource)
            ?->respond($resource) : null;
    }
}
