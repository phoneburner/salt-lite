<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Event;

use Laminas\Diactoros\ServerRequest;
use PhoneBurner\SaltLite\Http\Event\MiddlewareProcessingStart;
use PhoneBurner\SaltLite\Http\Middleware\LazyMiddleware;
use PhoneBurner\SaltLite\Http\Middleware\NullMiddleware;
use PhoneBurner\SaltLite\Logging\LogEntry;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class MiddlewareProcessingStartTest extends TestCase
{
    #[Test]
    public function constructorSetsPublicProperties(): void
    {
        $request = new ServerRequest();
        $middleware = $this->createMock(MiddlewareInterface::class);

        $event = new MiddlewareProcessingStart($middleware, $request);

        self::assertSame($middleware, $event->middleware);
        self::assertSame($request, $event->request);
    }

    #[Test]
    public function getLogEntryReturnsLogEntryWithMiddlewareClass(): void
    {
        $request = new ServerRequest();
        $middleware = $this->createMock(MiddlewareInterface::class);

        $event = new MiddlewareProcessingStart($middleware, $request);
        $log_entry = $event->getLogEntry();

        self::assertInstanceOf(LogEntry::class, $log_entry);
        self::assertSame('Processing Request with Middleware: {middleware}', $log_entry->message);
        self::assertArrayHasKey('middleware', $log_entry->context);
        self::assertSame($middleware::class, $log_entry->context['middleware']);
    }

    #[Test]
    public function getLogEntryReturnsMiddlewarePropertyFromLazyMiddleware(): void
    {
        $request = new ServerRequest();
        $middleware_name = NullMiddleware::class;
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->with($middleware_name)->willReturn(new NullMiddleware());
        $lazy_middleware = LazyMiddleware::make($container, $middleware_name);

        $event = new MiddlewareProcessingStart($lazy_middleware, $request);

        $log_entry = $event->getLogEntry();
        self::assertInstanceOf(LogEntry::class, $log_entry);
        self::assertArrayHasKey('middleware', $log_entry->context);
        self::assertSame($middleware_name, $log_entry->context['middleware']);
    }
}
