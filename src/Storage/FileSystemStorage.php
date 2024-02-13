<?php

declare (strict_types=1);

namespace Golden\Storage;


final class FileSystemStorage implements Storage
{

    public function keep(string $name, string $subject): void
    {
        $this->ensureFolder($name);
        file_put_contents($name, $subject);
    }

    public function exists(string $name): bool
    {
        return file_exists($name);
    }

    public function retrieve(string $name): string
    {
        if (!$this->exists($name)) {
            throw SnapshotNotFound::withName($name);
        }
        return file_get_contents($name);
    }

    private function ensureFolder(string $name): void
    {
        $folder = dirname($name);
        if (!file_exists($folder)) {
            mkdir($folder, 0775, true);
        }
    }
}
