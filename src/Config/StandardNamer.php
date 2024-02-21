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

final class StandardNamer implements Namer
{

    public function name(TestCase $test, string $prefix): string
    {
        $testFileName = (new ReflectionClass($test))->getFileName();
        $testCaseName = (new ReflectionClass($test))->getShortName();

        $name = $this->testName($test);
        $testName = $this->camelCaseToSnakeCase($name);

        return $this->createPath(dirname($testFileName), $prefix, $testCaseName, $testName);
    }

    private function camelCaseToSnakeCase($input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }

    private function createPath(...$parts): string
    {
        $flattened = [];

        array_walk_recursive($parts, function ($item) use (&$flattened) {
            $flattened[] = $item;
        });

        return join(DIRECTORY_SEPARATOR, $flattened);
    }

    private function testName(TestCase $test): string
    {
        if ($this->olderPhpUnit($test)) {
            return $test->getName();
        }
        return $test->name();
    }

    /**
     * We could need to support some versions of phpunit that uses getName()
     * instead of name()
     */
    private function olderPhpUnit(TestCase $test): bool
    {
        return !method_exists($test, 'name');
    }
}
