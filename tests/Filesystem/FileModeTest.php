<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Filesystem;

use PhoneBurner\SaltLite\Filesystem\FileMode;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class FileModeTest extends TestCase
{
    #[Test]
    public function enumHasCorrectValues(): void
    {
        self::assertSame('r', FileMode::Read->value);
        self::assertSame('r+', FileMode::ReadWriteOverwriteExisting->value);
        self::assertSame('w', FileMode::WriteCreateOrTruncateExisting->value);
        self::assertSame('w+', FileMode::ReadWriteCreateOrTruncateExisting->value);
        self::assertSame('a', FileMode::WriteCreateOrAppendExisting->value);
        self::assertSame('a+', FileMode::ReadWriteCreateOrAppendExisting->value);
        self::assertSame('x', FileMode::WriteCreateOnly->value);
        self::assertSame('x+', FileMode::ReadWriteCreateOnly->value);
        self::assertSame('c', FileMode::WriteCreateOrOverwriteExisting->value);
        self::assertSame('c+', FileMode::ReadWriteCreateOrOverwriteExisting->value);
    }

    #[Test]
    #[DataProvider('stringCastProvider')]
    public function castConvertsStringToEnum(string $input, FileMode|null $expected): void
    {
        $result = FileMode::cast($input);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function castWithEnumReturnsEnum(): void
    {
        $mode = FileMode::Read;
        $result = FileMode::cast($mode);
        self::assertSame($mode, $result);
    }

    #[Test]
    public function castWithNullReturnsNull(): void
    {
        $result = FileMode::cast(null);
        self::assertNull($result);
    }

    #[Test]
    public function castWithStringableConvertsToEnum(): void
    {
        $stringable = new class implements \Stringable {
            public function __toString(): string
            {
                return 'r';
            }
        };

        $result = FileMode::cast($stringable);
        self::assertSame(FileMode::Read, $result);
    }

    #[Test]
    public function castWithUnsupportedTypeReturnsNull(): void
    {
        $result = FileMode::cast(123);
        self::assertNull($result);

        $result = FileMode::cast([]);
        self::assertNull($result);

        $result = FileMode::cast(new \stdClass());
        self::assertNull($result);
    }

    #[Test]
    #[DataProvider('instanceStringProvider')]
    public function instanceConvertsStringToEnum(string $input, FileMode $expected): void
    {
        $result = FileMode::instance($input);
        self::assertSame($expected, $result);
    }

    #[Test]
    public function instanceThrowsForInvalidInput(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        FileMode::instance('invalid');
    }

    #[Test]
    #[DataProvider('readableModeProvider')]
    public function isReadableReturnsCorrectValue(FileMode $mode, bool $expected): void
    {
        $result = $mode->isReadable();
        self::assertSame($expected, $result);
    }

    #[Test]
    #[DataProvider('writableModeProvider')]
    public function isWritableReturnsCorrectValue(FileMode $mode, bool $expected): void
    {
        $result = $mode->isWritable();
        self::assertSame($expected, $result);
    }

    /**
     * @return array<string, array{string, ?FileMode}>
     */
    public static function stringCastProvider(): array
    {
        return [
            'r' => ['r', FileMode::Read],
            'r+' => ['r+', FileMode::ReadWriteOverwriteExisting],
            'w' => ['w', FileMode::WriteCreateOrTruncateExisting],
            'w+' => ['w+', FileMode::ReadWriteCreateOrTruncateExisting],
            'a' => ['a', FileMode::WriteCreateOrAppendExisting],
            'a+' => ['a+', FileMode::ReadWriteCreateOrAppendExisting],
            'x' => ['x', FileMode::WriteCreateOnly],
            'x+' => ['x+', FileMode::ReadWriteCreateOnly],
            'c' => ['c', FileMode::WriteCreateOrOverwriteExisting],
            'c+' => ['c+', FileMode::ReadWriteCreateOrOverwriteExisting],
            'uppercase R' => ['R', FileMode::Read],
            'with b flag' => ['rb', FileMode::Read],
            'with t flag' => ['rt', FileMode::Read],
            'with both flags' => ['r+bt', FileMode::ReadWriteOverwriteExisting],
            'invalid mode' => ['q', null],
            'empty string' => ['', null],
        ];
    }

    /**
     * @return array<string, array{string, FileMode}>
     */
    public static function instanceStringProvider(): array
    {
        return [
            'r' => ['r', FileMode::Read],
            'r+' => ['r+', FileMode::ReadWriteOverwriteExisting],
            'w' => ['w', FileMode::WriteCreateOrTruncateExisting],
            'w+' => ['w+', FileMode::ReadWriteCreateOrTruncateExisting],
            'a' => ['a', FileMode::WriteCreateOrAppendExisting],
            'a+' => ['a+', FileMode::ReadWriteCreateOrAppendExisting],
            'x' => ['x', FileMode::WriteCreateOnly],
            'x+' => ['x+', FileMode::ReadWriteCreateOnly],
            'c' => ['c', FileMode::WriteCreateOrOverwriteExisting],
            'c+' => ['c+', FileMode::ReadWriteCreateOrOverwriteExisting],
            'uppercase R' => ['R', FileMode::Read],
            'with b flag' => ['rb', FileMode::Read],
            'with t flag' => ['rt', FileMode::Read],
            'with both flags' => ['r+bt', FileMode::ReadWriteOverwriteExisting],
        ];
    }

    /**
     * @return array<string, array{FileMode, bool}>
     */
    public static function readableModeProvider(): array
    {
        return [
            'Read' => [FileMode::Read, true],
            'ReadWriteOverwriteExisting' => [FileMode::ReadWriteOverwriteExisting, true],
            'WriteCreateOrTruncateExisting' => [FileMode::WriteCreateOrTruncateExisting, false],
            'ReadWriteCreateOrTruncateExisting' => [FileMode::ReadWriteCreateOrTruncateExisting, true],
            'WriteCreateOrAppendExisting' => [FileMode::WriteCreateOrAppendExisting, false],
            'ReadWriteCreateOrAppendExisting' => [FileMode::ReadWriteCreateOrAppendExisting, true],
            'WriteCreateOnly' => [FileMode::WriteCreateOnly, false],
            'ReadWriteCreateOnly' => [FileMode::ReadWriteCreateOnly, true],
            'WriteCreateOrOverwriteExisting' => [FileMode::WriteCreateOrOverwriteExisting, false],
            'ReadWriteCreateOrOverwriteExisting' => [FileMode::ReadWriteCreateOrOverwriteExisting, true],
        ];
    }

    /**
     * @return array<string, array{FileMode, bool}>
     */
    public static function writableModeProvider(): array
    {
        return [
            'Read' => [FileMode::Read, false],
            'ReadWriteOverwriteExisting' => [FileMode::ReadWriteOverwriteExisting, true],
            'WriteCreateOrTruncateExisting' => [FileMode::WriteCreateOrTruncateExisting, true],
            'ReadWriteCreateOrTruncateExisting' => [FileMode::ReadWriteCreateOrTruncateExisting, true],
            'WriteCreateOrAppendExisting' => [FileMode::WriteCreateOrAppendExisting, true],
            'ReadWriteCreateOrAppendExisting' => [FileMode::ReadWriteCreateOrAppendExisting, true],
            'WriteCreateOnly' => [FileMode::WriteCreateOnly, true],
            'ReadWriteCreateOnly' => [FileMode::ReadWriteCreateOnly, true],
            'WriteCreateOrOverwriteExisting' => [FileMode::WriteCreateOrOverwriteExisting, true],
            'ReadWriteCreateOrOverwriteExisting' => [FileMode::ReadWriteCreateOrOverwriteExisting, true],
        ];
    }
}
