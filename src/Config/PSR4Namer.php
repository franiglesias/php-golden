<?php

declare (strict_types=1);

namespace Golden\Config;


use PHPUnit\Framework\TestCase;
use ReflectionClass;

final class PSR4Namer implements Namer
{

    private ComposerConfig $composerConfig;

    public function __construct()
    {
        $this->composerConfig = new ComposerConfig();
    }

    public function name(TestCase $test, string $prefix): string
    {
        $namespaceName = (new ReflectionClass($test))->getNamespaceName();
        $testCaseName = (new ReflectionClass($test))->getShortName();

        $name = $this->testName($test);
        $testName = $this->camelCaseToSnakeCase($name);

        return $this->createPath($this->namespaceParts($namespaceName), $prefix, $testCaseName, $testName);
    }

    private function namespaceParts(string $namespaceName): array
    {
        [$root, $base] = $this->psr4TestConfig();

        $parts = explode('\\', str_replace($root, "", $namespaceName));
        array_unshift($parts, $base);
        return $parts;
    }

    private function psr4TestConfig(): array
    {
        $psr4 = $this->composerConfig->key("autoload-dev.psr-4");
        $root = key($psr4);
        $base = $psr4[$root];
        return [$root, $base];
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
