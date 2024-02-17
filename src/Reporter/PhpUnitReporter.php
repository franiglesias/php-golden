<?php

declare (strict_types=1);

namespace Golden\Reporter;


use SebastianBergmann\Diff\Differ;
use SebastianBergmann\Diff\Output\UnifiedDiffOutputBuilder;

final class PhpUnitReporter implements Reporter
{

    public function report(string $snapshot, string $subject): string
    {
        if ($snapshot === $subject) {
            return "No differences found.";
        }

        // Show no differences message
        $builder = new UnifiedDiffOutputBuilder(
            "--- Previous\n+++ Actual\n",
            false
        );
        $differ = new Differ($builder);
        return sprintf("\nDifferences found:\n==================\n%s\n", $differ->diff($snapshot, $subject));
    }
}
