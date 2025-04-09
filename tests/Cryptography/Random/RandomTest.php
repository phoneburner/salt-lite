<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Random;

use PhoneBurner\SaltLite\Cryptography\Random\Random;
use PhoneBurner\SaltLite\Logging\LogLevel;
use PhoneBurner\SaltLite\Tests\Fixtures\CaselessEnum;
use PhoneBurner\SaltLite\Tests\Fixtures\NotAnEnum;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\TestCase;

final class RandomTest extends TestCase
{
    private const float PROBABILITY_THRESHOLD = 0.99999;

    #[Test]
    #[TestWith([1])]
    #[TestWith([16])]
    #[TestWith([256])]
    public function bytesReturnsExpectedLengthOfRandomBytes(int $length): void
    {
        $bytes = Random::make()->bytes($length);
        self::assertSame($length, \strlen($bytes));
    }

    #[Test]
    #[TestWith([0])]
    #[TestWith([-1])]
    public function bytesThrowsExceptionsWhenLengthLte0(int $length): void
    {
        $this->expectException(\UnexpectedValueException::class);
        Random::make()->bytes($length);
    }

    #[Test]
    #[TestWith([1])]
    #[TestWith([16])]
    #[TestWith([256])]
    public function hexReturnsExpectedLengthOfHexBytes(int $length): void
    {
        $bytes = Random::make()->hex($length);
        self::assertSame($length * 2, \strlen($bytes));
        self::assertMatchesRegularExpression('/^[0-9a-f]+$/', $bytes);
    }

    #[Test]
    #[TestWith([0])]
    #[TestWith([-1])]
    public function hexThrowsExceptionsWhenLengthLte0(int $length): void
    {
        $this->expectException(\UnexpectedValueException::class);
        Random::make()->bytes($length);
    }

    #[Test]
    public function intReturnsRandomIntSillyCase(): void
    {
        self::assertSame(42, Random::make()->int(42, 42));
    }

    #[Test]
    public function intReturnsRandomIntSmallRangeCase(): void
    {
        $int = Random::make()->int(0, 1);
        self::assertGreaterThanOrEqual(0, $int);
        self::assertLessThanOrEqual(1, $int);
    }

    #[Test]
    public function intReturnsRandomIntLargeRangeCase(): void
    {
        $int = Random::make()->int();
        self::assertGreaterThanOrEqual(\PHP_INT_MIN, $int);
        self::assertLessThanOrEqual(\PHP_INT_MAX, $int);
    }

    #[Test]
    public function intThrowsExceptionsWhenMinGtMax(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        Random::make()->int(43, 42);
    }

    #[Test]
    public function enumReturnsAnEnumInstanceFromPassedEnumClass(): void
    {
        $random = Random::make()->enum(LogLevel::class);
        self::assertInstanceOf(LogLevel::class, $random);
        self::assertContains($random, LogLevel::cases());
    }

    #[Test]
    public function enumReturnsARandomInstanceFromEntireEnumEnumeration(): void
    {
        $enums = \array_column(LogLevel::cases(), null, 'name');
        $count = \count($enums);
        $max = \ceil(\log(1 - self::PROBABILITY_THRESHOLD) / \log(($count - 1) / $count));
        for ($i = 0, $randoms = []; $i < $max; ++$i) {
            $enum = Random::make()->enum(LogLevel::class);
            $randoms[$enum->name] = $enum;
            if (\array_diff_key($enums, $randoms) === []) {
                self::assertEqualsCanonicalizing($enums, $randoms);
                return;
            }
        }

        self::fail(\vsprintf('All Enum Instances Were Not Randomly Returned within %s Iterations (at %s Probability)', [
            $max,
            self::PROBABILITY_THRESHOLD * 100 . '%',
        ]));
    }

    #[Test]
    public function enumThrowsExceptionIfPassedNotEnum(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        /** @phpstan-ignore argument.type, argument.templateType (intentional defect for testing) */
        Random::make()->enum(NotAnEnum::class);
    }

    #[Test]
    public function enumThrowsExceptionIfEnumHasNoCases(): void
    {
        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('Enum has no cases');
        Random::make()->enum(CaselessEnum::class);
    }
}
