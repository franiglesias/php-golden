<?php

declare (strict_types=1);

namespace Golden\Nomenclature;


use PHPUnit\Framework\TestCase;

final class DefaultNomenclature implements Nomenclature
{

    private string $prefix;
    private string $extension;

    public function __construct()
    {
        $this->prefix = "__snapshots";
        $this->extension = ".snap";
    }


    public function name(TestCase $test): string
    {

        $testName = $this->camelCaseToSnakeCase($test->name());

        $testCaseName = (new \ReflectionClass($test))->getShortName();

        $path = [
            $this->prefix,
            $testCaseName,
            $testName. $this->extension
        ];

        return join(DIRECTORY_SEPARATOR, $path);
    }

    function camelCaseToSnakeCase($input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
    }
}
