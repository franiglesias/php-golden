<?php

declare (strict_types=1);

namespace Golden\Master;


use JsonSerializable;

final class GMTest implements JsonSerializable
{

    private int $id;
    private array $params;
    private string $output;

    public function __construct(int $id, array $params, string $output)
    {
        $this->id = $id;
        $this->params = $params;
        $this->output = $output;
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
