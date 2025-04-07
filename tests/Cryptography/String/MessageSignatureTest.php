<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\String;

use PhoneBurner\SaltLite\Cryptography\Exception\InvalidStringLength;
use PhoneBurner\SaltLite\Cryptography\String\MessageSignature;
use PhoneBurner\SaltLite\String\Encoding\Encoding;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MessageSignatureTest extends TestCase
{
    #[Test]
    public function happy_path_test(): void
    {
        $bytes = \random_bytes(MessageSignature::LENGTH);

        $signature = new MessageSignature($bytes);

        self::assertSame($bytes, $signature->bytes());
        self::assertSame(MessageSignature::LENGTH, $signature->length());

        $encoded = $signature->export();
        self::assertEquals($signature, MessageSignature::import($encoded));
        self::assertMatchesRegularExpression(Encoding::BASE64URL_REGEX, (string)$signature);
    }

    #[Test]
    public function invalid_length_test_short(): void
    {
        $this->expectException(InvalidStringLength::class);
        new MessageSignature(\random_bytes(MessageSignature::LENGTH - 1));
    }

    #[Test]
    public function invalid_length_test_long(): void
    {
        $this->expectException(InvalidStringLength::class);
        new MessageSignature(\random_bytes(MessageSignature::LENGTH + 1));
    }

    #[Test]
    public function invalid_length_test_empty(): void
    {
        $this->expectException(InvalidStringLength::class);
        new MessageSignature('');
    }
}
