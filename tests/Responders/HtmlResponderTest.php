<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Responders;

use Frostaly\Data\Responders\HtmlResponder;
use Frostaly\Data\Tests\Resources\Resource;
use Frostaly\Template\TemplateEngine;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class HtmlResponderTest extends TestCase
{
    public function testRespond(): void
    {
        /** @var TemplateEngine&Stub $tempateEngine */
        $tempateEngine = $this->createStub(TemplateEngine::class);
        $tempateEngine->method('render')->willReturn('¯\_(ツ)_/¯');

        $responder = new HtmlResponder($tempateEngine, new Psr17Factory());
        $resource = new Resource(template: 'template');
        $response = $responder->respond($resource);

        $this->assertEquals('¯\_(ツ)_/¯', (string) $response->getBody());
        $this->assertContains('text/html', $response->getHeader('Content-type'));
    }
}
