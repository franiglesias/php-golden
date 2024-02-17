<?php

declare (strict_types=1);


use Golden\Storage\FileSystemStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Tests\Golden\Helpers\ExampleTest;
use Tests\Golden\Helpers\SnapshotAssertions;

final class FSVerifyTest extends TestCase
{
    use SnapshotAssertions;

    const EXPECTED_SNAPSHOT_PATH = "tests/Helpers/__snapshots/ExampleTest/example_test.snap";
    protected Storage $storage;
    private ExampleTest $testCase;
    private string $expectedPath;

    protected function setUp(): void
    {
        $this->storage = new FileSystemStorage();
        $this->testCase = new ExampleTest("ExampleTest");
        $this->testCase->registerStorage($this->storage);
        $this->expectedPath = getcwd() . DIRECTORY_SEPARATOR . self::EXPECTED_SNAPSHOT_PATH;
    }

    protected function tearDown(): void
    {
        unlink($this->expectedPath);
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
    public function shouldNormalizeSubjectToString(): void
    {
        $this->testCase->verify("This is the subject");

        $this->assertSnapshotContains($this->expectedPath, "This is the subject");
    }

    #[Test]
    /** @test */
    public function shouldNormalizeIntegerSubjectToString(): void
    {
        $this->testCase->verify(12345);

        $this->assertSnapshotContains($this->expectedPath, "12345");
    }

    #[Test]
    /** @test */
    public function shouldNormalizeFloatSubjectToString(): void
    {
        $this->testCase->verify(12345.678);

        $this->assertSnapshotContains($this->expectedPath, "12345.678");
    }

    #[Test]
    /** @test */
    public function shouldNormalizeArraySubjectToString(): void
    {
        $this->testCase->verify(["Item 1", "Item 2", "Item 3"]);

        $expected = <<<'EOD'
[
    "Item 1",
    "Item 2",
    "Item 3"
]
EOD;
        $this->assertSnapshotContains($this->expectedPath, $expected);
    }
}
