<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Event;

use Laminas\Diactoros\ServerRequest;
use PhoneBurner\SaltLite\Http\Event\HandlingLogoutRequest;
use PhoneBurner\SaltLite\Logging\LogEntry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class HandlingLogoutRequestTest extends TestCase
{
    #[Test]
    public function constructor_sets_public_properties(): void
    {
        $request = new ServerRequest();
        $event = new HandlingLogoutRequest($request);

        self::assertSame($request, $event->request);
    }

    #[Test]
    public function getLogEntry_returns_log_entry_with_message(): void
    {
        $request = new ServerRequest();
        $event = new HandlingLogoutRequest($request);
        $log_entry = $event->getLogEntry();

        self::assertInstanceOf(LogEntry::class, $log_entry);
        self::assertSame('Handling Logout Request', $log_entry->message);
        self::assertEmpty($log_entry->context);
    }
}
