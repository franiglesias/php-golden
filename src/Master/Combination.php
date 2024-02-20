<?php

declare (strict_types=1);

namespace Golden\Master;


final class Combination implements \JsonSerializable
{

    private int $id;
    private $params;
    private string $output;

    public function __construct(int $number, $value, string $result)
    {
        $this->id = $number;
        $this->params = $value;
        $this->output = $result;
    }


    public function jsonSerialize(): array
    {
        return [
            "id" => $this->id,
            "params" => $this->params,
            "output" => $this->output,
        ];

    }
}
