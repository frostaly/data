<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Responders;

use Frostaly\Data\Responders\JsonResponder;
use Frostaly\Data\Tests\Resources\Resource;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;

class JsonResponderTest extends TestCase
{
    public function testRespond(): void
    {
        $responder = new JsonResponder(new Psr17Factory());
        $resource = new Resource(ping: 'pong');
        $response = $responder->respond($resource);

        $this->assertEquals('{"ping":"pong"}', (string) $response->getBody());
        $this->assertContains('application/json', $response->getHeader('Content-type'));
    }
}
