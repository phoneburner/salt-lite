<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\I18n;

use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionCode;
use PhoneBurner\SaltLite\I18n\Subdivision\SubdivisionName;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class SubdivisionNameTest extends TestCase
{
    #[Test]
    public static function region_names_are_unique_and_non_empty(): void
    {
        $names = SubdivisionName::all();
        self::assertNotEmpty($names);
        self::assertCount(\count($names), \array_flip($names));
        self::assertCount(\count($names), \array_filter($names));
        foreach (\array_keys($names) as $key) {
            self::assertTrue(\defined(SubdivisionCode::class . '::' . $key));
        }
    }

    #[Test]
    public function display_returns_expected_string(): void
    {
        self::assertSame('Ohio', SubdivisionName::display('US-OH'));
    }

    #[Test]
    public function display_handles_invalid_case(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        /** @phpstan-ignore argument.type (Intentional Defect) */
        SubdivisionName::display('US-ZZ');
    }

    #[Test]
    public function short_returns_expected_string(): void
    {
        self::assertSame('OH', SubdivisionName::short('US-OH'));
    }

    #[Test]
    public function short_handles_invalid_case(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        /** @phpstan-ignore argument.type (Intentional Defect) */
        SubdivisionName::short('US-ZZ');
    }
}
