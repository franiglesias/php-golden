<?php

declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;

interface Scrubber
{
    public function clean(string $subject): string;
}
