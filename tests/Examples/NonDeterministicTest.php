<?php

declare (strict_types=1);

namespace Tests\Golden\Examples;

use Golden\Golden;
use Golden\Normalizer\Scrubber\RegexScrubber;
use PHPUnit\Framework\TestCase;
use function Golden\scrubbers;

final class NonDeterministicTest extends TestCase
{
    use Golden;

    #[Test]
    /** @test */
    public function shouldScrubNonDeterministicData(): void
    {
        $scrubber = new RegexScrubber("/\\d{2}:\\d{2}:\\d{2}.\\d{3}/", "<Current Time>");
        $subject = sprintf("Current time is: %s", (new \DateTimeImmutable())->format("H:i:s.v"));
        $this->verify($subject, scrubbers($scrubber));
    }

    #[Test]
    /** @test */
    public function shouldApplySeveralScrubbers(): void
    {
        $timeScrubber = new RegexScrubber("/\\d{2}:\\d{2}:\\d{2}.\\d{3}/", "<Current Time>");
        $textScrubber = new RegexScrubber("/Current time/", "Scrubbed time");
        $subject = sprintf("Current time is: %s", (new \DateTimeImmutable())->format("H:i:s.v"));
        $this->verify($subject, scrubbers($timeScrubber, $textScrubber));
    }
}
