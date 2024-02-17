<?php

declare (strict_types=1);

namespace FranIglesias\Golden\Normalizer;


final class JsonNormalizer implements Normalizer
{

    public function normalize($subject): string
    {
        $normalized = json_encode($subject, JSON_PRETTY_PRINT);
        return trim($normalized, '" ' . "\r");
    }
}
