<?php

declare (strict_types=1);

namespace Golden\Config;


use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class StandardNamer implements Namer
{

    public function __construct()
    {
    }

    public function name(TestCase $test, string $prefix): string
    {
        $fileName = (new ReflectionClass($test))->getFileName();
        $testCaseName = (new ReflectionClass($test))->getShortName();

        $name = $this->testName($test);
        $testName = $this->camelCaseToSnakeCase($name);

        return $this->createPath(dirname($fileName), $prefix, $testCaseName, $testName);
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

    public function testName(TestCase $test): string
    {
        if (method_exists($test, 'name')) {
            return $test->name();
        }
        /* This line is to support versions of PHPUnit previous to 11 */
        return $test->getName();
    }
}
