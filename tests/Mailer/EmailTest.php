<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Mailer;

use PhoneBurner\SaltLite\Domain\Email\EmailAddress;
use PhoneBurner\SaltLite\Mailer\Attachment;
use PhoneBurner\SaltLite\Mailer\Email;
use PhoneBurner\SaltLite\Mailer\MessageBody;
use PhoneBurner\SaltLite\Mailer\MessageBodyPart;
use PhoneBurner\SaltLite\Mailer\Priority;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class EmailTest extends TestCase
{
    #[Test]
    public function constructor_sets_subject_and_default_priority(): void
    {
        $email = new Email('Test Subject');

        self::assertSame('Test Subject', $email->getSubject());
        self::assertSame(Priority::Normal, $email->getPriority());
    }

    #[Test]
    public function constructor_sets_custom_priority(): void
    {
        $email = new Email('Test Subject', Priority::High);

        self::assertSame('Test Subject', $email->getSubject());
        self::assertSame(Priority::High, $email->getPriority());
    }

    #[Test]
    public function new_email_has_empty_recipients(): void
    {
        $email = new Email('Test Subject');

        self::assertEmpty($email->getTo());
        self::assertEmpty($email->getCc());
        self::assertEmpty($email->getBcc());
        self::assertEmpty($email->getFrom());
        self::assertEmpty($email->getReplyTo());
    }

    #[Test]
    public function add_to_adds_recipients(): void
    {
        $email = new Email('Test Subject');
        $address1 = new EmailAddress('test1@example.com');
        $address2 = new EmailAddress('test2@example.com');

        $result = $email->addTo($address1, $address2);

        self::assertSame($email, $result);
        self::assertCount(2, $email->getTo());
        self::assertSame($address1, $email->getTo()['test1@example.com']);
        self::assertSame($address2, $email->getTo()['test2@example.com']);
    }

    #[Test]
    public function add_cc_adds_cc_recipients(): void
    {
        $email = new Email('Test Subject');
        $address1 = new EmailAddress('test1@example.com');
        $address2 = new EmailAddress('test2@example.com');

        $result = $email->addCc($address1, $address2);

        self::assertSame($email, $result);
        self::assertCount(2, $email->getCc());
        self::assertSame($address1, $email->getCc()['test1@example.com']);
        self::assertSame($address2, $email->getCc()['test2@example.com']);
    }

    #[Test]
    public function add_bcc_adds_bcc_recipients(): void
    {
        $email = new Email('Test Subject');
        $address1 = new EmailAddress('test1@example.com');
        $address2 = new EmailAddress('test2@example.com');

        $result = $email->addBcc($address1, $address2);

        self::assertSame($email, $result);
        self::assertCount(2, $email->getBcc());
        self::assertSame($address1, $email->getBcc()['test1@example.com']);
        self::assertSame($address2, $email->getBcc()['test2@example.com']);
    }

    #[Test]
    public function add_from_adds_from_addresses(): void
    {
        $email = new Email('Test Subject');
        $address1 = new EmailAddress('test1@example.com');
        $address2 = new EmailAddress('test2@example.com');

        $result = $email->addFrom($address1, $address2);

        self::assertSame($email, $result);
        self::assertCount(2, $email->getFrom());
        self::assertSame($address1, $email->getFrom()['test1@example.com']);
        self::assertSame($address2, $email->getFrom()['test2@example.com']);
    }

    #[Test]
    public function add_reply_to_adds_reply_to_addresses(): void
    {
        $email = new Email('Test Subject');
        $address1 = new EmailAddress('test1@example.com');
        $address2 = new EmailAddress('test2@example.com');

        $result = $email->addReplyTo($address1, $address2);

        self::assertSame($email, $result);
        self::assertCount(2, $email->getReplyTo());
        self::assertSame($address1, $email->getReplyTo()['test1@example.com']);
        self::assertSame($address2, $email->getReplyTo()['test2@example.com']);
    }

    #[Test]
    public function set_text_body_sets_text_only_body(): void
    {
        $email = new Email('Test Subject');

        $result = $email->setTextBody('Plain text content');

        self::assertSame($email, $result);
        self::assertNotNull($email->getBody());
        self::assertNull($email->getBody()->html);
        self::assertNotNull($email->getBody()->text);
        self::assertSame('Plain text content', $email->getBody()->text->contents);
        self::assertSame(MessageBodyPart::DEFAULT_CHARSET, $email->getBody()->text->charset);
    }

    #[Test]
    public function set_html_body_sets_html_only_body(): void
    {
        $email = new Email('Test Subject');

        $result = $email->setHtmlBody('<p>HTML content</p>');

        self::assertSame($email, $result);
        self::assertNotNull($email->getBody());
        self::assertNotNull($email->getBody()->html);
        self::assertNull($email->getBody()->text);
        self::assertSame('<p>HTML content</p>', $email->getBody()->html->contents);
        self::assertSame(MessageBodyPart::DEFAULT_CHARSET, $email->getBody()->html->charset);
    }

    #[Test]
    public function set_text_body_preserves_html_body(): void
    {
        $email = new Email('Test Subject');
        $email->setHtmlBody('<p>HTML content</p>');

        $email->setTextBody('Plain text content');

        self::assertNotNull($email->getBody());
        self::assertNotNull($email->getBody()->html);
        self::assertNotNull($email->getBody()->text);
        self::assertSame('<p>HTML content</p>', $email->getBody()->html->contents);
        self::assertSame('Plain text content', $email->getBody()->text->contents);
    }

    #[Test]
    public function set_html_body_preserves_text_body(): void
    {
        $email = new Email('Test Subject');
        $email->setTextBody('Plain text content');

        $email->setHtmlBody('<p>HTML content</p>');

        self::assertNotNull($email->getBody());
        self::assertNotNull($email->getBody()->html);
        self::assertNotNull($email->getBody()->text);
        self::assertSame('<p>HTML content</p>', $email->getBody()->html->contents);
        self::assertSame('Plain text content', $email->getBody()->text->contents);
    }

    #[Test]
    public function set_body_sets_message_body(): void
    {
        $email = new Email('Test Subject');
        $body = new MessageBody(
            new MessageBodyPart('<p>HTML content</p>'),
            new MessageBodyPart('Plain text content'),
        );

        $result = $email->setBody($body);

        self::assertSame($email, $result);
        self::assertSame($body, $email->getBody());
    }

    #[Test]
    public function attach_adds_attachment(): void
    {
        $email = new Email('Test Subject');
        $attachment1 = Attachment::fromPath('test1.txt');
        $attachment2 = Attachment::fromContent('Test content', 'test2.txt');

        $result1 = $email->attach($attachment1);
        $result2 = $email->attach($attachment2);

        self::assertSame($email, $result1);
        self::assertSame($email, $result2);

        $attachments = $email->getAttachments();
        self::assertCount(2, $attachments);
        self::assertSame($attachment1, $attachments[0]);
        self::assertSame($attachment2, $attachments[1]);
    }
}
