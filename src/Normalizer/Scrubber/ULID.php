<?php

declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;


final class ULID implements Scrubber
{
    private RegexScrubber $scrubber;

    public function __construct(callable ...$options)
    {
        $this->scrubber = new RegexScrubber(
            "/[0-9A-Za-z]{26}/",
            "<ULID>",
            ...$options
        );
    }


    public function clean(string $subject): string
    {
        return $this->scrubber->clean($subject);
    }
}
