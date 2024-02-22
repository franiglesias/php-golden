<?php

declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;

interface Scrubber
{
    public function clean(string $subject): string;

    public function setContext(string $context);

    public function setReplacement(string $replacement);
}
