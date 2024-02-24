<?php

declare (strict_types=1);

namespace Tests\Golden;

use Golden\Storage\MemoryStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\ExampleTest;
use Tests\Golden\Helpers\SnapshotAssertions;
use function Golden\extension;
use function Golden\folder;
use function Golden\snapshot;
use function Golden\waitApproval;


final class ApprovalTest extends TestCase
{
    use SnapshotAssertions;

    protected Storage $storage;
    private ExampleTest $testCase;
    private string $expectedPath;

    protected function setUp(): void
    {
        $this->storage = new MemoryStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);
        $this->expectedPath = $this->absolute("tests/Helpers/__snapshots/ExampleTest/example_test.snap");
    }

    #[Test]
    /** @test */
    public function shouldNotPass(): void
    {
        try {
            $this->testCase->verify("This is the subject", waitApproval());
        } catch (AssertionFailedError $e) {
            $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/example_test.snap"));
        }
    }

    #[Test]
    /** @test */
    public function shouldPassAfterApproval(): void
    {
        try {
            $this->testCase->verify("This is the subject", waitApproval());
        } catch (AssertionFailedError $e) {
            $this->testCase->verify("This is the subject");
        }
    }

    #[Test]
    /** @test */
    public function shouldNotPassIfNeverApproved(): void
    {
        try {
            $this->testCase->verify("This is the subject", waitApproval());
        } catch (AssertionFailedError $e) {
            $this->expectException(AssertionFailedError::class);
            $this->testCase->verify("This is the subject", waitApproval());
        }
    }

    #[Test]
    /** @test */
    public function shouldChangeSnapshotName(): void
    {
        try {
            $this->testCase->verify("This is the subject", snapshot('my_snapshot'), waitApproval());
        } catch (AssertionFailedError $e) {
            $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/my_snapshot.snap"));
        }
    }

    #[Test]
    /** @test */
    public function shouldChangeFolderName(): void
    {
        try {
            $this->testCase->verify("This is the subject", folder('__my_folder'), waitApproval());
        } catch (AssertionFailedError $e) {
            $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__my_folder/ExampleTest/example_test.snap"));
        }
    }

    #[Test]
    /** @test */
    public function shouldChangeExtension(): void
    {
        try {
            $this->testCase->verify("This is the subject", extension('.tdata'), waitApproval());
        } catch (AssertionFailedError $e) {
            $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/example_test.tdata"));
        }
    }

    private function absolute(string $relative): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . $relative;
    }
}
