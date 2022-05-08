<?php

declare(strict_types=1);

namespace Frostaly\Data\Tests\Persistence\Adapters;

use Frostaly\Data\Persistence\Adapters\FilesystemStoreAdapter;

class FilesystemStoreAdapterTest extends AbstractStoreAdapterTest
{
    protected function setUp(): void
    {
        $this->store = new FilesystemStoreAdapter(
            $directory = sprintf('%s/../../Resources/filesystem', __DIR__),
        );
        array_map('unlink', array_filter((array) array_merge(glob($directory . '/*'))));

        $content = "<?php\n\nreturn new Frostaly\Data\Tests\Resources\Resource(%s);";
        file_put_contents($directory . '/resource.php', sprintf($content, ''));
        file_put_contents($directory . '/unsorted.php', sprintf($content, ''));
        file_put_contents($directory . '/first.php', sprintf($content, 'sort: -1'));
        file_put_contents($directory . '/last.php', sprintf($content, 'sort: 1'));
    }

    public function testInvalidUriFormat(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->store->find('(T_T)');
    }
}
