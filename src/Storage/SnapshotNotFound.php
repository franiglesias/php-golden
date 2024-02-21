<?php

declare (strict_types=1);

namespace Golden\Storage;


use RuntimeException;

final class SnapshotNotFound extends RuntimeException
{

    public static function withName(string $name): self
    {
        return new self(sprintf("Snapshot %s not found", $name));
    }
}
