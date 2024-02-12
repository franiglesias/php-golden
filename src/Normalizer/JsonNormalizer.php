<?php

declare (strict_types=1);

namespace Golden\Normalizer;


final class JsonNormalizer implements Normalizer
{

    public function normalize($subject): string
    {
        $normalized = json_encode($subject);
        return trim($normalized, '" ' . "\r");
    }
}
