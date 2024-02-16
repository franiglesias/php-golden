<?php

declare (strict_types=1);

namespace Tests\Golden\Config;

use Golden\Config;
use Golden\Config\PSR4Namer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class ConfigTest extends TestCase
{
    #[Test]
    /** @test */
    public function shouldUseTestCaseNameAsBaseName(): void
    {
        $config = new Config();
        $name = $config->name($this, new PSR4Namer());
        self::assertStringContainsString("ConfigTest", $name);
    }

    #[Test]
    /** @test */
    public function shouldUseTestNameAsSubName(): void
    {
        $config = new Config();
        $name = $config->name($this, new PSR4Namer());
        self::assertStringContainsString("should_use_test_name_as_sub_name", $name);
    }

    #[Test]
    /** @test */
    public function shouldUseSnapDefaultExtension(): void
    {
        $config = new Config();
        $name = $config->name($this, new PSR4Namer());
        self::assertStringContainsString(".snap", $name);
    }

    #[Test]
    /** @test */
    public function shouldCreateFullSnapshotName(): void
    {
        $config = new Config();
        $name = $config->name($this, new PSR4Namer());
        self::assertStringContainsString("tests/Config/__snapshots/ConfigTest/should_create_full_snapshot_name.snap", $name);
    }
}
