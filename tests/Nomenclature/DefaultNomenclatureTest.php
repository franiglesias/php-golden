<?php

declare (strict_types=1);

namespace Tests\Golden\Nomenclature;

use Golden\Nomenclature\DefaultNomenclature;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class DefaultNomenclatureTest extends TestCase
{
    #[Test]
    /** @test */
    public function shouldUseTestCaseNameAsBaseName(): void
    {
        $nomenclature = new DefaultNomenclature();
        $name = $nomenclature->name($this);
        self::assertStringContainsString("DefaultNomenclatureTest", $name);
    }

    #[Test]
    /** @test */
    public function shouldUseTestNameAsSubName(): void
    {
        $nomenclature = new DefaultNomenclature();
        $name = $nomenclature->name($this);
        self::assertStringContainsString("should_use_test_name_as_sub_name", $name);
    }

    #[Test]
    /** @test */
    public function shouldUseSnapDefaultExtension(): void
    {
        $nomenclature = new DefaultNomenclature();
        $name = $nomenclature->name($this);
        self::assertStringContainsString(".snap", $name);
    }

    #[Test]
    /** @test */
    public function shouldCreateFullSnapshotName(): void
    {
        $nomenclature = new DefaultNomenclature();
        $name = $nomenclature->name($this);
        self::assertStringContainsString("__snapshots/DefaultNomenclatureTest/should_create_full_snapshot_name.snap", $name);
    }
}
