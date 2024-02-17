<?php

declare (strict_types=1);

namespace FranIglesias\Golden\Config;

use PHPUnit\Framework\TestCase;

interface Namer
{
    public function name(TestCase $test, string $prefix): string;
}
