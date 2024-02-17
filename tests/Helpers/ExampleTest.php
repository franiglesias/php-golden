<?php

declare (strict_types=1);

namespace Tests\Golden\Helpers;

use Golden\Golden;
use PHPUnit\Framework\Attributes\DoesNotPerformAssertions;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    use Golden;

    #[Test]
    #[DoesNotPerformAssertions]
    /**
     * @test
     */
    public function testNothing(): void
    {
        $this->expectNotToPerformAssertions();
    }
}
