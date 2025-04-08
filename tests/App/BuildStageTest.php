<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\App;

use PhoneBurner\SaltLite\App\BuildStage;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class BuildStageTest extends TestCase
{
    #[Test]
    #[DataProvider('build_stage_values_provider')]
    public function enum_has_expected_values(string $name, string $value): void
    {
        $case = BuildStage::from($value);
        self::assertSame($value, $case->value);
        self::assertSame($name, $case->name);
    }

    #[Test]
    #[DataProvider('build_stage_values_provider')]
    public function instance_creates_enum_from_string(string $name, string $value): void
    {
        $case = BuildStage::instance($value);
        self::assertSame($value, $case->value);
        self::assertSame($name, $case->name);
    }

    #[Test]
    public function instance_is_case_insensitive(): void
    {
        $case = BuildStage::instance('PRODUCTION');
        self::assertSame('production', $case->value);
        self::assertSame('Production', $case->name);
    }

    #[Test]
    public function instance_throws_exception_for_invalid_value(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        BuildStage::instance('invalid');
    }

    #[Test]
    public function cast_returns_enum_for_valid_value(): void
    {
        $result = BuildStage::cast('production');
        self::assertNotNull($result);
        self::assertSame('production', $result->value);
        self::assertSame('Production', $result->name);
    }

    #[Test]
    public function cast_returns_null_for_invalid_value(): void
    {
        $result = BuildStage::cast('invalid');
        self::assertNull($result);
    }

    public static function build_stage_values_provider(): \Iterator
    {
        yield 'production' => ['Production', 'production'];
        yield 'integration' => ['Integration', 'integration'];
        yield 'development' => ['Development', 'development'];
    }
}
