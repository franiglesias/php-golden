<?php

declare (strict_types=1);

namespace Tests\Golden\Normalizer\Scrubber;

use Golden\Normalizer\Scrubber\CreditCard;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use function PHPUnit\Framework\assertEquals;

class CreditCardScrubberTest extends TestCase
{
    #[Test]
    /** @test */
    public function shouldObfuscateCreditCard(): void
    {
        $scrubber = new CreditCard();
        $subject = "Credit card: 1234-5678-9012-1234";
        assertEquals("Credit card: ****-****-****-1234", $scrubber->clean($subject));
    }
}
