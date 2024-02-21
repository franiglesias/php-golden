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
use function Golden\snapshot;


final class VerifyTest extends TestCase
{
    use SnapshotAssertions;

    const EXPECTED_SNAPSHOT_PATH = "tests/Helpers/__snapshots/ExampleTest/example_test.snap";

    protected Storage $storage;
    private ExampleTest $testCase;
    private string $expectedPath;

    protected function setUp(): void
    {
        $this->storage = new MemoryStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);
        $this->expectedPath = getcwd() . DIRECTORY_SEPARATOR . self::EXPECTED_SNAPSHOT_PATH;
    }

    #[Test]
    /** @test */
    public function shouldPass(): void
    {
        $this->testCase->verify("This is the subject");
        $this->assertSnapshotWasCreated($this->expectedPath);
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

        $expectedPath = str_replace("example_test", "my_snapshot", $this->expectedPath);

        $this->assertSnapshotWasCreated($expectedPath);
    }


}
