<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Logging;

use PhoneBurner\SaltLite\Logging\LogLevel;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel as Psr3LogLevel;

final class LogLevelTest extends TestCase
{
    #[Test]
    public function enum_values_match_psr3_values(): void
    {
        self::assertSame(Psr3LogLevel::EMERGENCY, LogLevel::Emergency->value);
        self::assertSame(Psr3LogLevel::ALERT, LogLevel::Alert->value);
        self::assertSame(Psr3LogLevel::CRITICAL, LogLevel::Critical->value);
        self::assertSame(Psr3LogLevel::ERROR, LogLevel::Error->value);
        self::assertSame(Psr3LogLevel::WARNING, LogLevel::Warning->value);
        self::assertSame(Psr3LogLevel::NOTICE, LogLevel::Notice->value);
        self::assertSame(Psr3LogLevel::INFO, LogLevel::Info->value);
        self::assertSame(Psr3LogLevel::DEBUG, LogLevel::Debug->value);
    }

    #[Test]
    #[DataProvider('instance_from_string_provider')]
    public function instance_from_string_returns_correct_enum(string $input, LogLevel $expected): void
    {
        $result = LogLevel::instance($input);
        self::assertSame($expected, $result);
    }

    #[Test]
    #[DataProvider('instance_from_int_provider')]
    public function instance_from_int_returns_correct_enum(int $input, LogLevel $expected): void
    {
        $result = LogLevel::instance($input);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function instance_from_enum_returns_same_instance(): void
    {
        $level = LogLevel::Error;
        $result = LogLevel::instance($level);
        self::assertSame($level, $result);
    }

    #[Test]
    public function instance_from_object_with_to_psr_log_level_method(): void
    {
        $object = new class {
            public function toPsrLogLevel(): string
            {
                return 'error';
            }
        };

        $result = LogLevel::instance($object);
        self::assertSame(LogLevel::Error, $result);
    }

    #[Test]
    public function instance_throws_for_invalid_int(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        LogLevel::instance(999);
    }

    #[Test]
    public function instance_throws_for_invalid_type(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        LogLevel::instance([]);
    }

    #[Test]
    #[DataProvider('monolog_level_provider')]
    public function to_monlog_log_level_returns_correct_value(LogLevel $level, int $expected): void
    {
        $result = $level->toMonlogLogLevel();
        self::assertSame($expected, $result);
    }

    /**
     * @return \Iterator<string, array{string, \PhoneBurner\SaltLite\Logging\LogLevel}>
     */
    public static function instance_from_string_provider(): \Iterator
    {
        yield 'emergency' => ['emergency', LogLevel::Emergency];
        yield 'alert' => ['alert', LogLevel::Alert];
        yield 'critical' => ['critical', LogLevel::Critical];
        yield 'error' => ['error', LogLevel::Error];
        yield 'warning' => ['warning', LogLevel::Warning];
        yield 'notice' => ['notice', LogLevel::Notice];
        yield 'info' => ['info', LogLevel::Info];
        yield 'debug' => ['debug', LogLevel::Debug];
        yield 'EMERGENCY uppercase' => ['EMERGENCY', LogLevel::Emergency];
        yield 'Error mixed case' => ['Error', LogLevel::Error];
        yield 'DEBUG uppercase' => ['DEBUG', LogLevel::Debug];
    }

    /**
     * @return \Iterator<string, array{int, \PhoneBurner\SaltLite\Logging\LogLevel}>
     */
    public static function instance_from_int_provider(): \Iterator
    {
        yield 'emergency (600)' => [600, LogLevel::Emergency];
        yield 'alert (550)' => [550, LogLevel::Alert];
        yield 'critical (500)' => [500, LogLevel::Critical];
        yield 'error (400)' => [400, LogLevel::Error];
        yield 'warning (300)' => [300, LogLevel::Warning];
        yield 'notice (250)' => [250, LogLevel::Notice];
        yield 'info (200)' => [200, LogLevel::Info];
        yield 'debug (100)' => [100, LogLevel::Debug];
    }

    /**
     * @return \Iterator<string, array{\PhoneBurner\SaltLite\Logging\LogLevel, int}>
     */
    public static function monolog_level_provider(): \Iterator
    {
        yield 'emergency' => [LogLevel::Emergency, 600];
        yield 'alert' => [LogLevel::Alert, 550];
        yield 'critical' => [LogLevel::Critical, 500];
        yield 'error' => [LogLevel::Error, 400];
        yield 'warning' => [LogLevel::Warning, 300];
        yield 'notice' => [LogLevel::Notice, 250];
        yield 'info' => [LogLevel::Info, 200];
        yield 'debug' => [LogLevel::Debug, 100];
    }
}
