<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Mailer;

use PhoneBurner\SaltLite\Mailer\Attachment;
use PhoneBurner\SaltLite\Mailer\AttachmentType;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class AttachmentTest extends TestCase
{
    #[Test]
    public function constructor_throws_exception_when_no_path_or_content(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Attachment must have a file path or content');
        new Attachment();
    }

    #[Test]
    public function constructor_throws_exception_when_both_path_and_content(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Attachment cannot have both a file path and content');
        new Attachment('path', 'content');
    }

    #[Test]
    public function attachment_from_path_sets_correct_type(): void
    {
        $attachment = new Attachment('test.txt');
        self::assertSame('test.txt', $attachment->path);
        self::assertSame('', $attachment->content);
        self::assertNull($attachment->name);
        self::assertNull($attachment->content_type);
        self::assertFalse($attachment->inline);
        self::assertSame(AttachmentType::AttachFromPath, $attachment->type);
    }

    #[Test]
    public function attachment_from_content_sets_correct_type(): void
    {
        $attachment = new Attachment('', 'test content');
        self::assertSame('', $attachment->path);
        self::assertSame('test content', $attachment->content);
        self::assertNull($attachment->name);
        self::assertNull($attachment->content_type);
        self::assertFalse($attachment->inline);
        self::assertSame(AttachmentType::AttachFromContent, $attachment->type);
    }

    #[Test]
    public function inline_attachment_from_path_sets_correct_type(): void
    {
        $attachment = new Attachment('test.txt', '', null, null, true);
        self::assertSame('test.txt', $attachment->path);
        self::assertSame('', $attachment->content);
        self::assertNull($attachment->name);
        self::assertNull($attachment->content_type);
        self::assertTrue($attachment->inline);
        self::assertSame(AttachmentType::EmbedFromPath, $attachment->type);
    }

    #[Test]
    public function inline_attachment_from_content_sets_correct_type(): void
    {
        $attachment = new Attachment('', 'test content', null, null, true);
        self::assertSame('', $attachment->path);
        self::assertSame('test content', $attachment->content);
        self::assertNull($attachment->name);
        self::assertNull($attachment->content_type);
        self::assertTrue($attachment->inline);
        self::assertSame(AttachmentType::EmbedFromContent, $attachment->type);
    }

    #[Test]
    public function from_path_factory_method_creates_path_attachment(): void
    {
        $attachment = Attachment::fromPath('test.txt', 'custom.txt', 'text/plain', false);
        self::assertSame('test.txt', $attachment->path);
        self::assertSame('', $attachment->content);
        self::assertSame('custom.txt', $attachment->name);
        self::assertSame('text/plain', $attachment->content_type);
        self::assertFalse($attachment->inline);
        self::assertSame(AttachmentType::AttachFromPath, $attachment->type);
    }

    #[Test]
    public function from_content_factory_method_creates_content_attachment(): void
    {
        $attachment = Attachment::fromContent('test content', 'custom.txt', 'text/plain', false);
        self::assertSame('', $attachment->path);
        self::assertSame('test content', $attachment->content);
        self::assertSame('custom.txt', $attachment->name);
        self::assertSame('text/plain', $attachment->content_type);
        self::assertFalse($attachment->inline);
        self::assertSame(AttachmentType::AttachFromContent, $attachment->type);
    }

    #[Test]
    public function from_path_factory_method_creates_inline_attachment(): void
    {
        $attachment = Attachment::fromPath('test.txt', 'custom.txt', 'text/plain', true);
        self::assertSame('test.txt', $attachment->path);
        self::assertSame('', $attachment->content);
        self::assertSame('custom.txt', $attachment->name);
        self::assertSame('text/plain', $attachment->content_type);
        self::assertTrue($attachment->inline);
        self::assertSame(AttachmentType::EmbedFromPath, $attachment->type);
    }

    #[Test]
    public function from_content_factory_method_creates_inline_attachment(): void
    {
        $attachment = Attachment::fromContent('test content', 'custom.txt', 'text/plain', true);
        self::assertSame('', $attachment->path);
        self::assertSame('test content', $attachment->content);
        self::assertSame('custom.txt', $attachment->name);
        self::assertSame('text/plain', $attachment->content_type);
        self::assertTrue($attachment->inline);
        self::assertSame(AttachmentType::EmbedFromContent, $attachment->type);
    }
}
