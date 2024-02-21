<?php

declare (strict_types=1);

namespace Golden\Config;


use PHPUnit\Framework\TestCase;
use ReflectionClass;

/*
 * StandardNamer is a Namer implementation that builds snapshot names
 * so they are stored in the same path as the test they are serving.
 * This allows users to locate test data easily.
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
