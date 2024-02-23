<?php

declare (strict_types=1);

namespace Tests\Golden\Examples;

use Golden\Golden;
use Golden\Normalizer\Scrubber\RegexScrubber;
use Golden\Normalizer\Scrubber\Scrubber;
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    /** @test */
    public function shouldAllowCustomScrubbers(): void
    {
        $scrubber = new MyTimeScrubber();
        $subject = sprintf("Current time is: %s", (new \DateTimeImmutable())->format("H:i:s.v"));
        $this->verify($subject, scrubbers($scrubber));
    }
}

class MyTimeScrubber implements Scrubber
{
    private RegexScrubber $scrubber;

    public function __construct(callable ...$options)
    {
        $this->scrubber = new RegexScrubber(
            "/\\d{2}:\\d{2}:\\d{2}.\\d{3}/",
            "<Current Time>",
            ...$options
        );
    }


    public function clean(string $subject): string
    {
        return $this->scrubber->clean($subject);
    }

    public function setContext(string $context)
    {
        $this->scrubber->setContext($context);
    }

    public function setReplacement(string $replacement)
    {
        $this->scrubber->setReplacement($replacement);
    }
}
