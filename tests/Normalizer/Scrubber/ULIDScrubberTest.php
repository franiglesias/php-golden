<?php

declare (strict_types=1);

namespace Tests\Golden\Normalizer\Scrubber;

use Golden\Normalizer\Scrubber\ULID;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function Golden\Normalizer\Scrubber\replacement;
use function PHPUnit\Framework\assertEquals;

final class ULIDScrubberTest extends TestCase
{
    #[Test]
    /** @test */
    public function shouldReplaceULID(): void
    {
        $scrubber = new ULID();
        $subject = "This is an ULID: 01HNAZ89E30JHFNJGQ84QFJBP3";
        assertEquals("This is an ULID: <ULID>", $scrubber->clean($subject));
    }

    #[Test]
    /** @test */
    public function shouldReplaceULIDWithCustomReplacement(): void
    {
        $scrubber = new ULID(replacement("[[Another thing]]"));
        $subject = "This is an ULID: 01HNAZ89E30JHFNJGQ84QFJBP3";
        assertEquals("This is an ULID: [[Another thing]]", $scrubber->clean($subject));
    }

    #[Test]
    /** @test */
    public function shouldReplaceULIDWithAnotherULID(): void
    {
        $scrubber = new ULID(replacement("01HNB9N6T6DEB1XN10C58DT1WE"));
        $subject = "This is an ULID: 01HNAZ89E30JHFNJGQ84QFJBP3";
        assertEquals("This is an ULID: 01HNB9N6T6DEB1XN10C58DT1WE", $scrubber->clean($subject));
    }
}
