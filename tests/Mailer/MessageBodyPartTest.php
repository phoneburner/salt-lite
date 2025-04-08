<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Mailer;

use PhoneBurner\SaltLite\Mailer\MessageBodyPart;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MessageBodyPartTest extends TestCase
{
    #[Test]
    public function constructor_sets_properties(): void
    {
        $part = new MessageBodyPart('Test content', 'iso-8859-1');

        self::assertSame('Test content', $part->contents);
        self::assertSame('iso-8859-1', $part->charset);
    }

    #[Test]
    public function constructor_uses_default_charset_when_not_specified(): void
    {
        $part = new MessageBodyPart('Test content');

        self::assertSame('Test content', $part->contents);
        self::assertSame(MessageBodyPart::DEFAULT_CHARSET, $part->charset);
        self::assertSame('utf-8', $part->charset);
    }

    #[Test]
    public function empty_constructor_creates_empty_content(): void
    {
        $part = new MessageBodyPart();

        self::assertSame('', $part->contents);
        self::assertSame(MessageBodyPart::DEFAULT_CHARSET, $part->charset);
    }
}
