<?php

declare (strict_types=1);

namespace Golden\Master;


final class Combinations
{
    private array $params;

    private function __construct(array $params)
    {
        $this->params = $params;
    }

    public static function of(array ...$params): Combinations
    {
        return new self($params);
    }

    public function all(): array
    {
        $result = [[]];
        foreach ($this->params as $parameter) {
            $tmp = [];
            foreach ($parameter as $value) {
                foreach ($result as $combination) {
                    $combination[] = $value;
                    $tmp[] = $combination;
                }
            }
            $result = $tmp;
        }
        return $result;
    }
}
