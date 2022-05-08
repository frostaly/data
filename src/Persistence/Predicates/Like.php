<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Predicates;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\PredicateInterface;

class Like implements PredicateInterface
{
    public function __construct(
        public string $field,
        public string $pattern,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function check(AbstractData $resource): bool
    {
        return isset($resource->{$this->field})
            ? $this->match($this->pattern, (string) $resource->{$this->field})
            : false;
    }

    /**
     * Perform a sql like match.
     */
    protected function match(string $pattern, string $subject): bool
    {
        $pattern = preg_quote($pattern, '/');
        $pattern = str_replace(['%', '_'], ['.*?', '.'], $pattern);
        return (bool) preg_match('/^' . $pattern . '$/su', $subject);
    }
}
