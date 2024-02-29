<?php

declare (strict_types=1);

namespace Tests\Golden;

use Golden\Storage\MemoryStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\ExampleTest;
use Tests\Golden\Helpers\SnapshotAssertions;
use function Golden\extension;
use function Golden\folder;
use function Golden\snapshot;


final class DefaultsTest extends TestCase
{
    use SnapshotAssertions;

    protected Storage $storage;
    private ExampleTest $testCase;

    protected function setUp(): void
    {
        $this->storage = new MemoryStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);
    }


    #[Test]
    /** @test */
    public function shouldUseDefaultFolder(): void
    {
        $this->testCase->defaults(Folder("testdata"));
        $this->testCase->verify("First subject", Snapshot("first"));
        $this->testCase->verify("Second subject", Snapshot("second"));
        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/testdata/ExampleTest/first.snap"));
        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/testdata/ExampleTest/second.snap"));
    }

    #[Test]
    /** @test */
    public function shouldUseDefaultExtension(): void
    {
        $this->testCase->defaults(Extension(".json"));
        $this->testCase->verify("First subject", Snapshot("first"));
        $this->testCase->verify("Second subject", Snapshot("second"));
        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/first.json"));
        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/second.json"));
    }

    #[Test]
    /** @test */
    public function shouldNotAllowDefaultSnapshotName(): void
    {
        $this->testCase->defaults(Snapshot("my-snapshot"));
        $this->testCase->verify("First subject");
        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/example_test.snap"));
    }


    private function absolute(string $relative): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . $relative;
    }
}
