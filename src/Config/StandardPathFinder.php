<?php

declare (strict_types=1);

namespace Golden\Config;


use PHPUnit\Framework\TestCase;
use ReflectionClass;

/*
 * StandardPathFinder is a PathFinder implementation that make a guess
 * about the path in which the TestCase file is located. This way, snapshots will
 * be located along the tests
 *
 * */

final class StandardPathFinder implements PathFinder
{

    public function path(TestCase $test, string $prefix): string
    {
        $testFileName = (new ReflectionClass($test))->getFileName();
        $testCaseName = (new ReflectionClass($test))->getShortName();

        return $this->createPath(dirname($testFileName), $prefix, $testCaseName);
    }


    private function createPath(...$parts): string
    {
        $flattened = [];

        array_walk_recursive($parts, function ($item) use (&$flattened) {
            $flattened[] = $item;
        });

        return join(DIRECTORY_SEPARATOR, $flattened) . DIRECTORY_SEPARATOR;
    }
}
