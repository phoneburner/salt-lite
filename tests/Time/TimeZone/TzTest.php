<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Time\TimeZone;

use DateTimeZone;
use PhoneBurner\SaltLite\Time\TimeZone\Tz;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(Tz::class)]
final class TzTest extends TestCase
{
    #[Test]
    #[DataProvider('provideCasesAndValues')]
    public function caseHasCorrectStringValue(Tz $tz, string $expected_value): void
    {
        self::assertSame($expected_value, $tz->value);
    }

    public static function provideCasesAndValues(): \Generator
    {
        yield 'UTC' => [Tz::Utc, 'UTC'];
        yield 'New York' => [Tz::NewYork, 'America/New_York'];
        yield 'Los Angeles' => [Tz::LosAngeles, 'America/Los_Angeles'];
        yield 'London' => [Tz::London, 'Europe/London'];
        yield 'Tokyo' => [Tz::Tokyo, 'Asia/Tokyo'];
        yield 'Sydney' => [Tz::Sydney, 'Australia/Sydney'];
        yield 'Johannesburg' => [Tz::Johannesburg, 'Africa/Johannesburg'];
        yield 'Honolulu' => [Tz::Honolulu, 'Pacific/Honolulu'];
    }

    #[Test]
    #[DataProvider('provideCasesForTimezone')]
    public function timezoneReturnsCorrectDatetimezoneObject(Tz $tz): void
    {
        $dateTimeZone = $tz->timezone();
        self::assertInstanceOf(DateTimeZone::class, $dateTimeZone);
        self::assertSame($tz->value, $dateTimeZone->getName());
    }

    public static function provideCasesForTimezone(): \Generator
    {
        yield 'UTC' => [Tz::Utc];
        yield 'New York' => [Tz::NewYork];
        yield 'Los Angeles' => [Tz::LosAngeles];
        yield 'London' => [Tz::London];
        yield 'Tokyo' => [Tz::Tokyo];
    }
}
