<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Adapters;

use Frostaly\Data\AbstractData;
use Frostaly\Data\Contracts\StoreAdapterInterface;
use Frostaly\Data\Persistence\Query;
use Frostaly\VarExporter\VarExporter;

class FilesystemStoreAdapter implements StoreAdapterInterface
{
    public function __construct(
        private string $directory,
    ) {}

    /**
     * {@inheritdoc}
     */
    public function all(Query $query): iterable
    {
        $iterator = new Filesystem\FinderIterator($this->directory, $this);
        $iterator = Filesystem\PredicateIterator::decorate($iterator, $query);
        $iterator = Filesystem\OrderingIterator::decorate($iterator, $query);
        return Filesystem\LimitIterator::decorate($iterator, $query);
    }

    /**
     * {@inheritdoc}
     */
    public function find(int|string $uri): ?AbstractData
    {
        $resource = @include $this->normalize($uri);
        if ($resource instanceof AbstractData) {
            $resource->uri = $uri;
            return $resource;
        }
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(AbstractData $resource): bool
    {
        $code = $this->export($resource);
        $file = $this->normalize($resource->uri);
        $result = file_put_contents($file, $code, LOCK_EX);
        touch($file, 7);
        @opcache_compile_file($file);
        return (bool) $result;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(AbstractData $resource): bool
    {
        if (isset($resource->uri)) {
            $file = $this->normalize($resource->uri);
            return unlink($file);
        }
        return false;
    }

    /**
     * Export the resource to PHP code.
     */
    protected function export(AbstractData $resource): string
    {
        $uri = $resource->uri ?? uniqid();
        unset($resource->uri);
        $code = VarExporter::export($resource);
        $resource->uri = $uri;
        return "<?php\n\nreturn $code;";
    }

    /**
     * Get the normalized file path for the given uri.
     */
    protected function normalize(int|string $uri): string
    {
        if (!preg_match('/^[a-zA-Z0-9\/_-]*$/', (string) $uri)) {
            throw new \InvalidArgumentException(
                sprintf('The format of the URI "%s" is invalid.', $uri),
            );
        }
        return $this->directory . "/{$uri}.php";
    }
}
