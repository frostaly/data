<?php

declare(strict_types=1);

namespace Frostaly\Data\Persistence\Adapters\Filesystem;

use FilesystemIterator as FS;
use Frostaly\Data\AbstractData;
use Frostaly\Data\Persistence\Adapters\FilesystemStoreAdapter;

/**
 * @extends \IteratorIterator<string,AbstractData,\Iterator>
 */
class FinderIterator extends \IteratorIterator
{
    public function __construct(
        string $directory,
        private FilesystemStoreAdapter $adapter,
    ) {
        parent::__construct(new \RegexIterator(
            iterator: new \RecursiveDirectoryIterator(
                $directory,
                FS::CURRENT_AS_PATHNAME | FS::KEY_AS_FILENAME | FS::SKIP_DOTS,
            ),
            pattern: '/^[a-zA-Z0-9\/_-]*\.php$/',
            flags: \RegexIterator::USE_KEY,
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function key(): string
    {
        return substr(parent::key(), 0, -4);
    }

    /**
     * {@inheritdoc}
     */
    public function current(): AbstractData
    {
        $resource = $this->adapter->find($this->key());
        assert($resource instanceof AbstractData);
        return $resource;
    }
}
