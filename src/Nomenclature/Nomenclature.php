<?php

declare (strict_types=1);

namespace Golden\Nomenclature;

use PHPUnit\Framework\TestCase;

interface Nomenclature
{
    public function name(TestCase $test): string;
}
