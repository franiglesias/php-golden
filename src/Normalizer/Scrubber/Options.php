<?php
declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;

function format(string $format): \Closure
{
    return fn(Scrubber $scrubber) => $scrubber->setContext($format);
}

function replacement(string $replacement): \Closure
{
    return fn(Scrubber $scrubber) => $scrubber->setReplacement($replacement);
}
