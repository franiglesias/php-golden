<?php

declare (strict_types=1);

namespace FranIglesias\Golden\Storage;


final class MemoryStorage implements Storage
{
    private array $data;

    public function __construct()
    {
        $this->data = [];
    }

    public function keep(string $name, string $subject): void
    {
        $this->data[$name] = $subject;
    }

    public function exists(string $name): bool
    {
        return isset($this->data[$name]);
    }

    public function retrieve(string $name): string
    {
        if (!$this->exists($name)) {
            throw SnapshotNotFound::withName($name);
        }
        return $this->data[$name];
    }
}
