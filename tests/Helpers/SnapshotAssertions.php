<?php

declare (strict_types=1);

namespace Tests\Golden\Helpers;

use function PHPUnit\Framework\assertEquals;

trait SnapshotAssertions
{
    public function assertSnapshotContains(string $name, string $content): void
    {
        $snapshot = $this->storage->retrieve($name);
        assertEquals($content, $snapshot);
    }

    public function assertSnapshotWasCreated(string $name): void
    {
        $this->assertTrue($this->storage->exists($name));
    }

    public function absolutePath(string $name): string
    {
        return $this->absolute($name);
    }
}
