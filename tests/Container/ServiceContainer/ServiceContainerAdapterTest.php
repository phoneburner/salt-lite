<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceContainer;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\ServiceContainer\ServiceContainerAdapter;
use PhoneBurner\SaltLite\Http\Session\SessionId;
use PhoneBurner\SaltLite\MessageBus\MessageBus;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class ServiceContainerAdapterTest extends TestCase
{
    public function test_it_can_get_a_service(): void
    {
        $app = $this->createMock(App::class);

        $sut = new ServiceContainerAdapter($app);

        self::assertFalse($sut->has(MessageBus::class));
        self::assertFalse($sut->has(SessionId::class, true));
        self::assertTrue($sut->has(SessionId::class, false));

        /** @var MessageBus&MockObject $message_bus */
        $message_bus = $this->createMock(MessageBus::class);
        $sut->set(MessageBus::class, $message_bus);
        self::assertTrue($sut->has(MessageBus::class));
        self::assertSame($message_bus, $sut->get(MessageBus::class));

        $session_id = SessionId::generate();
        $sut->set(SessionId::class, fn(App $app): SessionId => $session_id);
        self::assertTrue($sut->has(SessionId::class, true));
        self::assertSame($session_id, $sut->get(SessionId::class));
    }
}
