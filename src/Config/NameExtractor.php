<?php

declare (strict_types=1);

namespace Golden\Config;


use PHPUnit\Framework\TestCase;

final class NameExtractor
{

    public function name(TestCase $test): string
    {
        return $this->camelCaseToSnakeCase($this->testName($test));
    }

    private function camelCaseToSnakeCase($input): string
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $input));
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
