<?php

declare(strict_types=1);

namespace Frostaly\Data;

use Frostaly\Data\Contracts\ResponderInterface;
use Psr\Http\Message\ServerRequestInterface;

class ResponderNegociator
{
    public function __construct(
        private ResponderLocator $responderLocator,
    ) {}

    /**
     * Negociate the responder for the given request and resource.
     */
    public function negociate(ServerRequestInterface $request, AbstractData $resource): ?ResponderInterface
    {
        if (!$resource::$responders) {
            return null;
        }
        /** @var ?\Negotiation\Accept $result */
        $result = (new \Negotiation\Negotiator())->getBest(
            $request->getHeaderLine('Accept') ?: '*/*',
            array_keys($resource::$responders),
        );
        $format = $result?->getValue() ?? array_key_first($resource::$responders);
        return $this->responderLocator->get($resource::$responders[$format]);
    }
}
