<?php

declare(strict_types=1);

namespace Frostaly\Data;

abstract class AbstractData implements \JsonSerializable
{
    public int|string $uri;
    public string $template;

    /**
     * @var string[]
     */
    public static array $responders = [
        'text/html' => Responders\HtmlResponder::class,
        'application/json' => Responders\JsonResponder::class,
    ];

    /**
     * Get public properties as an array.
     */
    public function toArray(): array
    {
        return array_filter(
            (array) $this,
            fn($key) => $key[0] !== "\0",
            ARRAY_FILTER_USE_KEY,
        );
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
}
