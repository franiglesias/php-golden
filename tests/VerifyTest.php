<?php

declare (strict_types=1);

namespace Tests\Golden;

use Golden\Storage\MemoryStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;

final class VerifyTest extends TestCase
{
    use SnapshotAssertions;
    const EXPECTED_SNAPSHOT_PATH = "__snapshots/ExampleTest/example_test.snap";
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
    public function shouldPass(): void
    {
        $this->testCase->verify("This is the subject");
        $this->assertSnapshotWasCreated(self::EXPECTED_SNAPSHOT_PATH);
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
    public function shouldNormalizeSubjectToString(): void
    {
        $this->testCase->verify("This is the subject");

        $this->assertSnapshotContains(self::EXPECTED_SNAPSHOT_PATH, "This is the subject");
    }

    #[Test]
    /** @test */
    public function shouldNormalizeIntegerSubjectToString(): void
    {
        $this->testCase->verify(12345);

        $this->assertSnapshotContains(self::EXPECTED_SNAPSHOT_PATH, "12345");
    }

    #[Test]
    /** @test */
    public function shouldNormalizeFloatSubjectToString(): void
    {
        $this->testCase->verify(12345.678);

        $this->assertSnapshotContains(self::EXPECTED_SNAPSHOT_PATH, "12345.678");
    }

    #[Test]
    /** @test */
    public function shouldNormalizeArraySubjectToString(): void
    {
        $this->testCase->verify(["Item 1", "Item 2", "Item 3"]);

        $this->assertSnapshotContains(self::EXPECTED_SNAPSHOT_PATH, '["Item 1","Item 2","Item 3"]');
    }
}
