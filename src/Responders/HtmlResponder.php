<?php

declare(strict_types=1);

namespace Frostaly\Data\Responders;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\ResponderInterface;
use Frostaly\Template\TemplateEngine;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class HtmlResponder implements ResponderInterface
{
    public function __construct(
        protected TemplateEngine $templateEngine,
        protected ResponseFactoryInterface $responseFactory,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function respond(AbstractData $resource): ResponseInterface
    {
        $response = $this->responseFactory->createResponse();
        $response->getBody()->write($this->render($resource));
        return $response->withHeader('Content-type', 'text/html');
    }

    /**
     * Render the resource to HTML string.
     */
    protected function render(AbstractData $resource): string
    {
        return $this->templateEngine->render($resource->template, [
            'data' => $resource,
            'renderer' => $this->templateEngine,
        ]);
    }
}
