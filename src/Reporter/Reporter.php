<?php

declare (strict_types=1);

namespace FranIglesias\Golden\Reporter;

interface Reporter
{
    public function report(string $snapshot, string $subject): string;
}
