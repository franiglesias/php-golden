<?php

declare (strict_types=1);

namespace Tests\Golden;

use Golden\Golden;
use Golden\Storage\MemoryStorage;
use Golden\Storage\Storage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;


final class VerifyTest extends TestCase
{
    protected Storage $storage;

    protected function setUp(): void
    {
        $this->storage = new MemoryStorage();
    }

    #[Test]
        /** @test */
    public function shouldPass(): void
    {
        $testCase = new class("Example test") extends TestCase {
            use Golden;
        };
        $testCase->registerStorage($this->storage);

        $testCase->verify("This is the subject");
        $this->assertTrue($this->storage->exists("Example test"));
    }

    #[Test]
    /** @test */
    public function shouldNotPass(): void
    {
        $testCase = new class("Example test") extends TestCase {
            use Golden;
        };
        $testCase->registerStorage($this->storage);

        $testCase->verify("This is the subject");

        $this->expectException(ExpectationFailedException::class);
        $testCase->Verify("This is another output");
    }

    #[Test]
    /** @test */
    public function shouldNormalizeSubjectToString(): void
    {
        $testCase = new class("Example test") extends TestCase {
            use Golden;
        };
        $testCase->registerStorage($this->storage);

        $testCase->verify("This is the subject");

        $snapshot = $this->storage->retrieve("Example test");

        self::assertEquals("This is the subject", $snapshot);
    }

    #[Test]
    /** @test */
    public function shouldNormalizeIntegerSubjectToString(): void
    {
        $testCase = new class("Example test") extends TestCase {
            use Golden;
        };
        $testCase->registerStorage($this->storage);

        $testCase->verify(12345);

        $snapshot = $this->storage->retrieve("Example test");

        self::assertEquals("12345", $snapshot);
    }

    #[Test]
    /** @test */
    public function shouldNormalizeFloatSubjectToString(): void
    {
        $testCase = new class("Example test") extends TestCase {
            use Golden;
        };
        $testCase->registerStorage($this->storage);

        $testCase->verify(12345.678);

        $snapshot = $this->storage->retrieve("Example test");

        self::assertEquals("12345.678", $snapshot);
    }

    #[Test]
    /** @test */
    public function shouldNormalizeArraySubjectToString(): void
    {
        $testCase = new class("Example test") extends TestCase {
            use Golden;
        };
        $testCase->registerStorage($this->storage);

        $testCase->verify(["Item 1", "Item 2", "Item 3"]);

        $snapshot = $this->storage->retrieve("Example test");

        self::assertEquals('["Item 1","Item 2","Item 3"]', $snapshot);
    }

}
