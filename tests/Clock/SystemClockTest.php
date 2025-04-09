<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Clock;

use PhoneBurner\SaltLite\Clock\SystemClock;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SystemClockTest extends TestCase
{
    #[Test]
    public function happyPath(): void
    {
        $before = new \DateTimeImmutable();
        $now = new SystemClock()->now();
        $after = new \DateTimeImmutable();

        self::assertGreaterThanOrEqual($before, $now);
        self::assertLessThanOrEqual($after, $now);
    }
}
