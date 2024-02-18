<?php

declare (strict_types=1);

namespace Golden\Reporter;

/*
 * Reporter
 *
 * defines the role of an object that can show differences between the snapshot and the subject
 *
 * */

interface Reporter
{
    public const NO_DIFFERENCES_FOUND = "No differences found.";
    public const DIFFERENCES_TEMPLATE = "\nDifferences found:\n==================\n%s\n";

    public function report(string $snapshot, string $subject): string;
}
