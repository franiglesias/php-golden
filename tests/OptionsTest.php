<?php

declare (strict_types=1);

namespace Tests\Golden;

use Golden\Config;
use Golden\Storage\MemoryStorage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\ExampleTest;
use Tests\Golden\Helpers\SnapshotAssertions;
use function Golden\extension;


final class OptionsTest extends TestCase
{
    use SnapshotAssertions;

    private MemoryStorage $storage;
    private ExampleTest $testCase;
    const EXPECTED_SNAPSHOT_PATH = "tests/Helpers/__snapshots/ExampleTest/example_test.snap";

    #[Test]
    /** @test */
    public function shouldApplyFolderOption(): void
    {
        $this->storage = new MemoryStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);

        $this->testCase->verify("something", Config::folder("__folder"));

        $this->assertSnapshotWasCreated("tests/Helpers/__folder/ExampleTest/example_test.snap");
    }

    #[Test]
    /** @test */
    public function shouldApplyExtensionOption(): void
    {
        $this->storage = new MemoryStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);

        $this->testCase->verify("something", extension(".dat"));

        $this->assertSnapshotWasCreated("tests/Helpers/__snapshots/ExampleTest/example_test.dat");
    }
}
