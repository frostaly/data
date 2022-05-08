<?php

declare(strict_types=1);

namespace Frostaly\Data\Responders;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\ResponderInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class JsonResponder implements ResponderInterface
{
    public function __construct(
        protected ResponseFactoryInterface $responseFactory,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function respond(AbstractData $resource): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write(json_encode($resource, JSON_THROW_ON_ERROR));
        return $response->withHeader('Content-type', 'application/json');
    }
}
