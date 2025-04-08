<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Mailer;

use PhoneBurner\SaltLite\Mailer\MessageBody;
use PhoneBurner\SaltLite\Mailer\MessageBodyPart;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class MessageBodyTest extends TestCase
{
    #[Test]
    public function constructor_sets_properties(): void
    {
        $html = new MessageBodyPart('<p>HTML content</p>', 'utf-8');
        $text = new MessageBodyPart('Plain text content', 'utf-8');

        $body = new MessageBody($html, $text);

        self::assertSame($html, $body->html);
        self::assertSame($text, $body->text);
    }

    #[Test]
    public function constructor_accepts_null_values(): void
    {
        $html = new MessageBodyPart('<p>HTML content</p>', 'utf-8');
        $body1 = new MessageBody($html, null);
        self::assertSame($html, $body1->html);
        self::assertNull($body1->text);

        $text = new MessageBodyPart('Plain text content', 'utf-8');
        $body2 = new MessageBody(null, $text);
        self::assertNull($body2->html);
        self::assertSame($text, $body2->text);
    }

    #[Test]
    public function empty_constructor_creates_null_body_parts(): void
    {
        $body = new MessageBody();
        self::assertNull($body->html);
        self::assertNull($body->text);
    }
}
