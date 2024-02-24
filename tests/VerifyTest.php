<?php

declare (strict_types=1);

namespace Tests\Golden;

use Golden\Storage\MemoryStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\ExampleTest;
use Tests\Golden\Helpers\SnapshotAssertions;
use function Golden\extension;
use function Golden\folder;
use function Golden\snapshot;


final class VerifyTest extends TestCase
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
    public function shouldPass(): void
    {
        $this->testCase->verify("This is the subject");
        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/example_test.snap"));
    }

    #[Test]
    /** @test */
    public function shouldNotPass(): void
    {
        $this->testCase->verify("This is the subject");

        $this->expectException(ExpectationFailedException::class);
        $this->testCase->Verify("This is another output");
    }

    #[Test]
    /** @test */
    public function shouldChangeSnapshotName(): void
    {
        $this->testCase->verify("This is the subject", snapshot('my_snapshot'));

        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/my_snapshot.snap"));
    }

    #[Test]
    /** @test */
    public function shouldChangeFolderName(): void
    {
        $this->testCase->verify("This is the subject", folder('__my_folder'));

        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__my_folder/ExampleTest/example_test.snap"));
    }

    #[Test]
    /** @test */
    public function shouldChangeExtension(): void
    {
        $this->testCase->verify("This is the subject", extension('.tdata'));

        $this->assertSnapshotWasCreated($this->absolute("tests/Helpers/__snapshots/ExampleTest/example_test.tdata"));
    }

    private function absolute(string $relative): string
    {
        return getcwd() . DIRECTORY_SEPARATOR . $relative;
    }
}
