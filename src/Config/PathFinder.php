<?php

declare (strict_types=1);

namespace Golden\Config;

use PHPUnit\Framework\TestCase;

/*
 * PathFinder
 *
 * defines the role of building a file path for the snapshot based on the location of the test being executed
 *
 * */

interface PathFinder
{
    public function path(TestCase $test, string $prefix): string;
}
