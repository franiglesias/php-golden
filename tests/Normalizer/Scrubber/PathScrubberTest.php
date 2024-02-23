<?php

declare (strict_types=1);

namespace Tests\Golden\Normalizer\Scrubber;

use Golden\Normalizer\Scrubber\PathScrubber;
use PHPUnit\Framework\TestCase;
use function Golden\Normalizer\Scrubber\replacement;
use function PHPUnit\Framework\assertEquals;

final class PathScrubberTest extends TestCase
{
    #[Test]
    /** @test */
    public function shouldNotReplaceAnythingIfNoPath(): void
    {
        $subject = "A string not suspicious of containing anything to remove";
        $scrubber = new PathScrubber("some.path", "<Replacement>");
        assertEquals($subject, $scrubber->clean($subject));
    }

    #[Test]
    /** @test */
    public function shouldReplaceSimplePath(): void
    {
        $subject = '{"object":{"id":"12345","name":"My Object","count":1234,"validated":true,"other":{"remark":"accept"}}}';
        $scrubber = new PathScrubber("object", "<Replacement>");
        $expected = <<<'EOF'
{
    "object": "<Replacement>"
}
EOF;
        assertEquals($expected, $scrubber->clean($subject));
    }

    #[Test]
    /** @test */
    public function shouldReplaceInnerPath(): void
    {
        $subject = '{"object":{"id":"12345","name":"My Object","count":1234,"validated":true,"other":{"remark":"accept"}}}';
        $scrubber = new PathScrubber("object.other.remark", "<Replacement>");
        $expected = /** @lang JSON */
            <<<'EOF'
{
    "object": {
        "id": "12345",
        "name": "My Object",
        "count": 1234,
        "validated": true,
        "other": {
            "remark": "<Replacement>"
        }
    }
}
EOF;
        assertEquals($expected, $scrubber->clean($subject));
    }

    #[Test]
    /** @test */
    public function shouldOverrideReplacement(): void
    {
        $subject = '{"object":{"id":"12345","name":"My Object","count":1234,"validated":true,"other":{"remark":"accept"}}}';
        $scrubber = new PathScrubber("object", "<Replacement>", replacement("<#############>"));
        $expected = <<<'EOF'
{
    "object": "<#############>"
}
EOF;
        assertEquals($expected, $scrubber->clean($subject));
    }
}
