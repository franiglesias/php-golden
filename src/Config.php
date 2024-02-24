<?php

declare (strict_types=1);

namespace Golden;


use Golden\Config\PathFinder;
use Golden\Config\TestNameExtractor;
use PHPUnit\Framework\TestCase;

final class Config
{

    private string $prefix;
    private string $extension;
    private bool $approval;
    private string $snapshot;
    private TestNameExtractor $nameExtractor;
    private array $scrubbers;

    public function __construct()
    {
        $this->prefix = "__snapshots";
        $this->extension = ".snap";
        $this->approval = false;
        $this->snapshot = '';
        $this->nameExtractor = new TestNameExtractor();
        $this->scrubbers = [];
    }

    public function name(TestCase $test, PathFinder $pathFinder): string
    {
        $testName = $this->snapshotFileName($test);

        $pathWithTrailingSlash = $pathFinder->path($test, $this->prefix);
        return $pathWithTrailingSlash . $testName . $this->extension;
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function setSnapshot(string $snapshot)
    {
        $this->snapshot = $snapshot;
    }


    public function waitApproval(): void
    {
        $this->approval = true;
    }

    public function approvalMode(): bool
    {
        return $this->approval;
    }

    private function snapshotFileName(TestCase $test): string
    {
        if ($this->snapshot != '') {
            return $this->snapshot;
        }
        return $this->nameExtractor->name($test);
    }

    public function setScrubbers(array $scrubbers): void
    {
        $this->scrubbers = $scrubbers;
    }

    public function scrubbers(): array
    {
        return $this->scrubbers;
    }
}
