<?php

declare (strict_types=1);

namespace Golden\Normalizer\Scrubber;


final class CreditCard implements Scrubber
{
    private RegexScrubber $scrubber;

    public function __construct(callable ...$options)
    {
        $this->scrubber = new RegexScrubber(
            "/\\d{4}-\\d{4}-\\d{4}-/",
            "****-****-****-",
            ...$options
        );
    }


    public function clean(string $subject): string
    {
        return $this->scrubber->clean($subject);
    }
}
