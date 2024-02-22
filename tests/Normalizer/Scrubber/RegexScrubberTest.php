<?php

declare (strict_types=1);

namespace Tests\Golden\Normalizer\Scrubber;

use Golden\Normalizer\Scrubber\RegexScrubber;
use PHPUnit\Framework\TestCase;
use function Golden\Normalizer\Scrubber\format;
use function Golden\Normalizer\Scrubber\replacement;
use function PHPUnit\Framework\assertEquals;


final class RegexScrubberTest extends TestCase
{
    #[Test]
    /** @test */
    public function shouldNotReplaceAnything(): void
    {
        $subject = "A string not suspicions of contain anything to remove";
        $scrubber = new RegexScrubber('/\d{2}-\d{2}-\d{2}/', '24-01-15');
        assertEquals($subject, $scrubber->clean($subject));
    }

    #[Test]
    /** @test */
    public function shouldReplaceDate(): void
    {
        $subject = "The next days 24-01-30, 24-02-03 and 24-02-10 we will be closed.";
        $scrubber = new RegexScrubber("/\\d{2}-\\d{2}-\\d{2}/", "24-01-15");
        $result = $scrubber->clean($subject);
        $expected = "The next days 24-01-15, 24-01-15 and 24-01-15 we will be closed.";
        assertEquals($expected, $result);
    }

    #[Test]
    /** @test */
    public function shouldApplyFormatModifierToLimitReplacements(): void
    {
        $subject = "The next days 24-01-30, 24-02-03 and 24-02-10 we will be closed.";
        $scrubber = new RegexScrubber("/\\d{2}-\\d{2}-\\d{2}/", "**-**-**", format("days %s"));
        $result = $scrubber->clean($subject);
        $expected = "The next days **-**-**, 24-02-03 and 24-02-10 we will be closed.";
        assertEquals($expected, $result);
    }

    #[Test]
    /** @test */
    public function shouldOverrideReplacement(): void
    {
        $subject = "The next days 24-01-30, 24-02-03 and 24-02-10 we will be closed.";
        $scrubber = new RegexScrubber("/\\d{2}-\\d{2}-\\d{2}/", "24-01-15", replacement("##-##-##"));
        $result = $scrubber->clean($subject);
        $expected = "The next days ##-##-##, ##-##-## and ##-##-## we will be closed.";
        assertEquals($expected, $result);
    }

    #[Test]
    /** @test */
    public function shouldOverrideReplacementAndFormat(): void
    {
        $subject = "The next days 24-01-30, 24-02-03 and 24-02-10 we will be closed.";
        $scrubber = new RegexScrubber("/\\d{2}-\\d{2}-\\d{2}/", "24-01-15", replacement("##-##-##"), format("days %s"));
        $result = $scrubber->clean($subject);
        $expected = "The next days ##-##-##, 24-02-03 and 24-02-10 we will be closed.";
        assertEquals($expected, $result);
    }
}

