<?php

declare (strict_types=1);

namespace Golden\Storage;

/*
 * Storage
 *
 * Defines the role of an object that can store, retrieve and check for snapshot existence
 *
 * */

interface Storage
{
    public function keep(string $name, string $subject): void;

    public function exists(string $name): bool;

    public function retrieve(string $name): string;
}
