<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Clock;

use Carbon\CarbonImmutable;
use PhoneBurner\SaltLite\Clock\StaticClock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class StaticClockTest extends TestCase
{
    #[Test]
    public function happy_path(): void
    {
        $now = CarbonImmutable::now();

        $clock = new StaticClock($now);

        self::assertSame($now, $clock->now());
        \sleep(1);
        self::assertSame($now, $clock->now());
    }
}
