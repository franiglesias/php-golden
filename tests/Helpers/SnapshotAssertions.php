<?php

declare (strict_types=1);

namespace Tests\Golden\Helpers;

trait SnapshotAssertions
{
    public function assertSnapshotContains(string $name, string $content): void
    {
        $snapshot = $this->storage->retrieve($name);
        self::assertEquals($content, $snapshot);
    }

    public function assertSnapshotWasCreated(string $name): void
    {
        $this->assertTrue($this->storage->exists($name));
    }

    public function absolutePath(string $name): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . $name;
    }
}
