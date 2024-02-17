<?php

declare (strict_types=1);

namespace FranIglesias\Golden\Storage;


final class SnapshotNotFound extends \RuntimeException
{

    public static function withName(string $name): self
    {
        return new self(sprintf("Snapshot %s not found", $name));
    }
}
