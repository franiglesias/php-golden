<?php

declare (strict_types=1);

namespace FranIglesias\Golden\Normalizer;

interface Normalizer
{
    public function normalize($subject): string;
}
