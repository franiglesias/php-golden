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
            return Reporter::NO_DIFFERENCES_FOUND;
        }

        // Show no differences message
        $builder = new UnifiedDiffOutputBuilder(
            "--- Snapshot\n+++ Subject\n",
            false
        );
        $differ = new Differ($builder);
        return sprintf(self::DIFFERENCES_TEMPLATE, $differ->diff($snapshot, $subject));
    }
}
