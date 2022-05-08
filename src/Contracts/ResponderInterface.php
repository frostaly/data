<?php

declare(strict_types=1);

namespace Frostaly\Data\Contracts;

use Frostaly\Data\AbstractData;
use Psr\Http\Message\ResponseInterface;

interface ResponderInterface
{
    /**
     * Generate a response for the given resource.
     */
    public function respond(AbstractData $resource): ResponseInterface;
}
