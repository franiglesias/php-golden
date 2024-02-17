<?php

declare (strict_types=1);

namespace Tests\Golden;

use FranIglesias\Golden\Storage\MemoryStorage;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\FranIglesias\Golden\Helpers\ExampleTest;
use Tests\FranIglesias\Golden\Helpers\SnapshotAssertions;
use function FranIglesias\Golden\extension;
use function FranIglesias\Golden\folder;
use function FranIglesias\Golden\waitApproval;

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

        $this->testCase->verify("something", folder("__folder"));

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

    #[Test]
    /** @test */
    public function shouldApplyWaitApprovalOption(): void
    {
        $this->storage = new MemoryStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);

        $this->expectException(AssertionFailedError::class);
        $this->testCase->verify("something", waitApproval());

        $this->assertSnapshotWasCreated("tests/Helpers/__snapshots/ExampleTest/example_test.snap");
    }

    /** @test */
    public function shouldFailAlwaysWithApprovalOption(): void
    {
        $this->storage = new MemoryStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);

        $this->expectException(AssertionFailedError::class);
        $this->testCase->verify("something", waitApproval());

        $this->assertSnapshotWasCreated("tests/Helpers/__snapshots/ExampleTest/example_test.snap");
    }
}
