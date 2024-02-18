<?php

declare (strict_types=1);

namespace Tests\Golden\Storage;

use Golden\Storage\FileSystemStorage;
use Golden\Storage\SnapshotNotFound;
use Golden\Storage\Storage;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\SnapshotAssertions;

final class FileSystemStorageTest extends TestCase
{
    use SnapshotAssertions;

    private Storage $storage;
    private vfsStreamDirectory $vfs;

    protected function setUp(): void
    {
        $this->vfs = vfsStream::setup();
        $this->storage = new FileSystemStorage();
    }

    #[Test]
    /** @test */
    public function shouldWriteSnapshotInPath(): void
    {
        $snapshotPath = $this->pathInVfs("/my_snapshot.snap");
        $this->storage->keep($snapshotPath, "some content");
        $this->assertSnapshotWasCreated($snapshotPath);
    }

    #[Test]
    /** @test */
    public function shouldReadSnapshotInPath(): void
    {
        $snapshotPath = $this->pathInVfs("/my_snapshot.snap");
        $this->storage->keep($snapshotPath, "some content");
        $this->assertSnapshotContains($snapshotPath, "some content");
    }

    #[Test]
    /** @test */
    public function shouldFailWhenNotFound(): void
    {
        $this->expectException(SnapshotNotFound::class);
        $this->storage->retrieve("not_snapshot.here");
    }

    private function pathInVfs(string $snapshot): string
    {
        return $this->vfs->url() . $snapshot;
    }
}
