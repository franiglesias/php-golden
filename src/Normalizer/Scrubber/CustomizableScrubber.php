<?php

declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;

interface CustomizableScrubber
{
    public function setReplacement(string $replacement);
}
