<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Resources;

use Frostaly\Data\AbstractData;

#[AllowDynamicProperties]
class Resource extends AbstractData
{
    public function __construct(mixed ...$properties)
    {
        foreach ($properties as $property => $value) {
            $this->{$property} = $value;
        }
    }
}
