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
        foreach ($scenarios as $scenarioID => $scenario) {
            $subject[] = new GMTest(
                $scenarioID + 1,
                $scenario,
                ($this->sut)(...$scenario)
            );
        }
        return $subject;
    }
}
