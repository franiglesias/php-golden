<?php

declare (strict_types=1);

namespace Tests\Golden\Normalizer;

use Golden\Normalizer\JsonNormalizer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

final class JsonNormalizerTest extends TestCase
{
    private JsonNormalizer $normalizer;

    protected function setUp(): void
    {
        $this->normalizer = new JsonNormalizer();
    }

    #[Test]
    /** @test */
    public function shouldNormalizeString(): void
    {
        $normalized = $this->normalizer->normalize("This is the subject");

        assertEquals("This is the subject", $normalized);
    }

    #[Test]
    /** @test */
    public function shouldNormalizeInteger(): void
    {
        $normalized = $this->normalizer->normalize(12345);

        assertEquals(12345, $normalized);
    }

    #[Test]
    /** @test */
    public function shouldNormalizerFloat(): void
    {
        $normalized = $this->normalizer->normalize(12345.678);

        assertEquals(12345.678, $normalized);
    }

    #[Test]
    /** @test */
    public function shouldNormalizeArray(): void
    {
        $normalized = $this->normalizer->normalize(["Item 1", "Item 2", "Item 3"]);
        $expected = <<<'EOD'
[
    "Item 1",
    "Item 2",
    "Item 3"
]
EOD;

        assertEquals($expected, $normalized);
    }

    #[Test]
    /** @test */
    public function shouldNormalizeJSONString(): void
    {
        $subject = <<<EOD
{
    "some": "string",
    "another": {
        "string": "with data",
        "number": 1234,
        "float": 1234.5678,
        "boolean": true,
        "empty": null,
        "object": {
            "inside": "another"
        }
    }
}
EOD;


        $normalize = $this->normalizer->normalize($subject);
        $expected = json_encode(json_decode($subject, true), JSON_PRETTY_PRINT);
        assertEquals($expected, $normalize);
    }
}
