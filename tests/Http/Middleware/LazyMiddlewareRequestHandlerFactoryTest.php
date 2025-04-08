<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Middleware;

use PhoneBurner\SaltLite\Http\Middleware\LazyMiddlewareRequestHandlerFactory;
use PhoneBurner\SaltLite\Http\Middleware\MiddlewareQueue;
use PhoneBurner\SaltLite\Http\Middleware\MiddlewareStack;
use PhoneBurner\SaltLite\Http\Middleware\NullMiddleware;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyMiddlewareRequestHandlerFactoryTest extends TestCase
{
    private ContainerInterface&MockObject $container;

    private EventDispatcherInterface&MockObject $event_dispatcher;

    private RequestHandlerInterface&MockObject $fallback_handler;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->event_dispatcher = $this->createMock(EventDispatcherInterface::class);
        $this->fallback_handler = $this->createMock(RequestHandlerInterface::class);
    }

    #[Test]
    public function queue_returns_middleware_queue_with_resolved_middleware(): void
    {
        $middleware = new NullMiddleware();
        $this->container->method('get')
            ->with(NullMiddleware::class)
            ->willReturn($middleware);

        $factory = new LazyMiddlewareRequestHandlerFactory($this->container, $this->event_dispatcher);
        $queue = $factory->queue($this->fallback_handler, [NullMiddleware::class]);

        self::assertInstanceOf(MiddlewareQueue::class, $queue);
        $reflect = new \ReflectionClass($queue);
        $next = $reflect->getMethod('next')->invoke($queue);
        self::assertInstanceOf(MiddlewareInterface::class, $next);
        self::assertEquals($middleware, $next);
    }

    #[Test]
    public function stack_returns_middleware_stack_with_resolved_middleware(): void
    {
        $middleware = new NullMiddleware();
        $this->container->method('get')
            ->with(NullMiddleware::class)
            ->willReturn($middleware);

        $factory = new LazyMiddlewareRequestHandlerFactory($this->container, $this->event_dispatcher);
        $stack = $factory->stack($this->fallback_handler, [NullMiddleware::class]);

        self::assertInstanceOf(MiddlewareStack::class, $stack);
        $reflect = new \ReflectionClass($stack);
        $next = $reflect->getMethod('next')->invoke($stack);
        self::assertInstanceOf(MiddlewareInterface::class, $next);
        self::assertEquals($middleware, $next);
    }

    #[Test]
    public function queue_handles_middleware_instances_directly(): void
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $this->container->expects($this->never())
            ->method('get');

        $factory = new LazyMiddlewareRequestHandlerFactory($this->container, $this->event_dispatcher);
        $queue = $factory->queue($this->fallback_handler, [$middleware]);

        self::assertInstanceOf(MiddlewareQueue::class, $queue);
    }

    #[Test]
    public function stack_handles_middleware_instances_directly(): void
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $this->container->expects($this->never())
            ->method('get');

        $factory = new LazyMiddlewareRequestHandlerFactory($this->container, $this->event_dispatcher);
        $stack = $factory->stack($this->fallback_handler, [$middleware]);

        self::assertInstanceOf(MiddlewareStack::class, $stack);
    }
}
