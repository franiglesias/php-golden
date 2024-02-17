<?php

declare (strict_types=1);

namespace FranIglesias\Golden\Storage;

interface Storage
{
    public function keep(string $name, string $subject): void;

    public function exists(string $name): bool;

    public function retrieve(string $name): string;
}
