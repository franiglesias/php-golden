<?php

declare (strict_types=1);

namespace Golden;


use Golden\Config\Namer;
use PHPUnit\Framework\TestCase;

final class Config
{

    private string $prefix;
    private string $extension;
    private bool $approval;

    public function __construct()
    {
        $this->prefix = "__snapshots";
        $this->extension = ".snap";
        $this->approval = false;
    }

    public function name(TestCase $test, Namer $namer): string
    {
        return $namer->name($test, $this->prefix()) . $this->extension();
    }

    public function setPrefix(string $prefix): void
    {
        $this->prefix = $prefix;
    }

    public function setExtension(string $extension): void
    {
        $this->extension = $extension;
    }

    public function waitApproval(): void
    {
        $this->approval = true;
    }

    public function approvalMode(): bool
    {
        return $this->approval;
    }

    private function extension(): string
    {
        return $this->extension;
    }

    private function prefix(): string
    {
        return $this->prefix;
    }
}
