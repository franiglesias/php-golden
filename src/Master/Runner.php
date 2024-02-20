<?php

declare (strict_types=1);

namespace Golden\Master;


final class Runner
{

    /** @var callable */
    private $sut;

    public function __construct(callable $sut)
    {
        $this->sut = $sut;
    }

    public function execute(array $scenarios): array
    {
        $subject = [];
        foreach ($scenarios as $key => $scenario) {
            $subject[] = new Test(
                $key + 1,
                $scenario,
                ($this->sut)(...$scenario));
        }
        return $subject;
    }
}
