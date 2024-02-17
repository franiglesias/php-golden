<?php

declare (strict_types=1);

namespace Tests\Golden\Storage;

use Golden\Storage\MemoryStorage;
use Golden\Storage\SnapshotNotFound;
use Golden\Storage\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\SnapshotAssertions;

final class MemoryStorageTest extends TestCase
{
    use SnapshotAssertions;

    private Storage $storage;

    protected function setUp(): void
    {
        $this->storage = new MemoryStorage();
    }

    #[Test]
    /** @test */
    public function shouldWriteSnapshotInPath(): void
    {
        $this->storage->keep("my_snapshot.snap", "some content");
        $this->assertSnapshotWasCreated("my_snapshot.snap");
    }

    #[Test]
    /** @test */
    public function shouldReadSnapshotInPath(): void
    {
        $this->storage->keep("my_snapshot.snap", "some content");
        $this->assertSnapshotContains("my_snapshot.snap", "some content");
    }

    #[Test]
    /** @test */
    public function shouldFailWhenNotFound(): void
    {
        $this->expectException(SnapshotNotFound::class);
        $this->storage->retrieve("not_snapshot.here");
    }
}
