<?php

declare (strict_types=1);

namespace Tests\Golden;

use Golden\Storage\FileSystemStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\ExampleTest;
use Tests\Golden\Helpers\SnapshotAssertions;
use function Golden\combinations;

final class GoldenMasterTest extends TestCase
{
    use SnapshotAssertions;

    const EXPECTED_SNAPSHOT_PATH = "tests/Helpers/__snapshots/ExampleTest/example_test.snap.json";

    protected Storage $storage;
    private ExampleTest $testCase;

    protected function setUp(): void
    {
        $this->storage = new FileSystemStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);
        $this->expectedPath = getcwd() . DIRECTORY_SEPARATOR . self::EXPECTED_SNAPSHOT_PATH;
    }

    #[Test]
    /** @test */
    public function shouldPass(): void
    {
        $sut = function (...$param) {
            return strtoupper($param[0]);
        };

        $collection = ["one", "two", "three"];

        $this->testCase->master($sut, combinations($collection));
        $this->assertSnapshotWasCreated($this->expectedPath);
    }
}
