<?php

declare (strict_types=1);

namespace Golden\Normalizer;

/*
 * Normalizer
 *
 * defines the rol of an object that can transform any data into a string that we can store or compare
 * 
 * */

interface Normalizer
{
    public function normalize($subject): string;
}
