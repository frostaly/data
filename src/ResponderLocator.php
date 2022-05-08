<?php

declare(strict_types=1);

namespace Frostaly\Data;

use Frostaly\Data\Contracts\ResponderInterface;
use Psr\Container\ContainerInterface;

class ResponderLocator
{
    public function __construct(
        private ContainerInterface $container,
    ) {}

    /**
     * Locate a responder by its identifier.
     */
    public function get(string $id): ResponderInterface
    {
        $responder = $this->container->get($id);
        return $responder instanceof ResponderInterface ? $responder : throw new \RuntimeException(
            sprintf('Entry "%s" needs to implement the ResponderInterface.', $id),
        );
    }
}
