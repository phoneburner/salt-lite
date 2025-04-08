<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Math;

use PhoneBurner\SaltLite\Math\Math;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MathTest extends TestCase
{
    #[Test]
    #[DataProvider('floor_provider')]
    public function floor_returns_integer_floor(int|float $input, int $expected): void
    {
        self::assertSame($expected, Math::floor($input));
    }

    public static function floor_provider(): \Iterator
    {
        yield 'integer' => [5, 5];
        yield 'negative integer' => [-5, -5];
        yield 'positive float' => [5.7, 5];
        yield 'negative float' => [-5.7, -6];
        yield 'zero' => [0, 0];
        yield 'zero point zero' => [0.0, 0];
        yield 'large float' => [1000000.999999, 1000000];
    }

    #[Test]
    #[DataProvider('ceil_provider')]
    public function ceil_returns_integer_ceiling(int|float $input, int $expected): void
    {
        self::assertSame($expected, Math::ceil($input));
    }

    public static function ceil_provider(): \Iterator
    {
        yield 'integer' => [5, 5];
        yield 'negative integer' => [-5, -5];
        yield 'positive float' => [5.7, 6];
        yield 'negative float' => [-5.7, -5];
        yield 'zero' => [0, 0];
        yield 'zero point zero' => [0.0, 0];
        yield 'large float' => [1000000.000001, 1000001];
    }

    #[Test]
    #[DataProvider('clamp_provider')]
    public function clamp_constrains_value_within_range(
        int|float $value,
        int|float $min,
        int|float $max,
        int|float $expected,
    ): void {
        self::assertSame($expected, Math::clamp($value, $min, $max));
    }

    public static function clamp_provider(): \Iterator
    {
        yield 'within range' => [5, 0, 10, 5];
        yield 'at min' => [0, 0, 10, 0];
        yield 'at max' => [10, 0, 10, 10];
        yield 'below min' => [-5, 0, 10, 0];
        yield 'above max' => [15, 0, 10, 10];
        yield 'float within range' => [5.5, 0, 10, 5.5];
        yield 'float below min' => [-5.5, 0, 10, 0];
        yield 'float above max' => [15.5, 0, 10, 10];
        yield 'negative range' => [-15, -20, -10, -15];
        yield 'zero range' => [5, 5, 5, 5];
    }

    #[Test]
    public function clamp_throws_exception_when_max_less_than_min(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        $this->expectExceptionMessage('max must be greater than or equal to min');
        Math::clamp(5, 10, 0);
    }

    #[Test]
    #[DataProvider('iclamp_provider')]
    public function iclamp_returns_integer_clamped_value(int|float $value, int $min, int $max, int $expected): void
    {
        self::assertSame($expected, Math::iclamp($value, $min, $max));
    }

    public static function iclamp_provider(): \Iterator
    {
        yield 'integer within range' => [5, 0, 10, 5];
        yield 'integer below min' => [-5, 0, 10, 0];
        yield 'integer above max' => [15, 0, 10, 10];
        yield 'float within range' => [5.5, 0, 10, 5];
        yield 'float below min' => [-5.5, 0, 10, 0];
        yield 'float above max' => [15.5, 0, 10, 10];
    }

    #[Test]
    #[DataProvider('fclamp_provider')]
    public function fclamp_returns_float_clamped_value(
        int|float $value,
        int|float $min,
        int|float $max,
        float $expected,
    ): void {
        self::assertSame($expected, Math::fclamp($value, $min, $max));
    }

    public static function fclamp_provider(): \Iterator
    {
        yield 'integer within range' => [5, 0, 10, 5.0];
        yield 'integer below min' => [-5, 0, 10, 0.0];
        yield 'integer above max' => [15, 0, 10, 10.0];
        yield 'float within range' => [5.5, 0, 10, 5.5];
        yield 'float below min' => [-5.5, 0, 10, 0.0];
        yield 'float above max' => [15.5, 0, 10, 10.0];
        yield 'float min and max' => [5.5, 1.5, 9.5, 5.5];
    }
}
