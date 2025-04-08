<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http\Middleware;

use Laminas\Diactoros\Response;
use Laminas\Diactoros\ServerRequest;
use PhoneBurner\SaltLite\Http\Middleware\LazyMiddleware;
use PhoneBurner\SaltLite\Http\Middleware\NullMiddleware;
use PhoneBurner\SaltLite\Http\Middleware\TerminableMiddleware;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class LazyMiddlewareTest extends TestCase
{
    private ContainerInterface&MockObject $container;

    private ServerRequestInterface $request;

    private RequestHandlerInterface&MockObject $handler;

    private ResponseInterface $response;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->request = new ServerRequest();
        $this->handler = $this->createMock(RequestHandlerInterface::class);
        $this->response = new Response();
    }

    #[Test]
    public function make_returns_lazy_middleware_instance(): void
    {
        $middleware = LazyMiddleware::make($this->container, NullMiddleware::class);
        self::assertInstanceOf(LazyMiddleware::class, $middleware);
    }

    #[Test]
    public function process_delegates_to_resolved_middleware(): void
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->expects($this->once())
            ->method('process')
            ->with($this->request, $this->handler)
            ->willReturn($this->response);

        $this->container->expects($this->once())
            ->method('get')
            ->with(NullMiddleware::class)
            ->willReturn($middleware);

        $lazy_middleware = LazyMiddleware::make($this->container, NullMiddleware::class);

        self::assertSame($this->response, $lazy_middleware->process($this->request, $this->handler));
    }

    #[Test]
    public function process_sets_fallback_handler_on_terminable_middleware(): void
    {
        $middleware = $this->createMock(TerminableMiddleware::class);
        $middleware->expects($this->once())
            ->method('setFallbackRequestHandler')
            ->with($this->handler);

        $middleware->expects($this->once())
            ->method('process')
            ->with($this->request, $this->handler)
            ->willReturn($this->response);

        $this->container->expects($this->once())
            ->method('get')
            ->with(NullMiddleware::class)
            ->willReturn($middleware);

        $lazy_middleware = LazyMiddleware::make($this->container, NullMiddleware::class);
        $lazy_middleware->setFallbackRequestHandler($this->handler);

        self::assertSame($this->response, $lazy_middleware->process($this->request, $this->handler));
    }

    #[Test]
    public function process_does_not_set_fallback_handler_on_non_terminable_middleware(): void
    {
        $middleware = $this->createMock(MiddlewareInterface::class);
        $middleware->expects($this->once())
            ->method('process')
            ->with($this->request, $this->handler)
            ->willReturn($this->response);

        $this->container->expects($this->once())
            ->method('get')
            ->with(NullMiddleware::class)
            ->willReturn($middleware);

        $lazy_middleware = LazyMiddleware::make($this->container, NullMiddleware::class);
        $lazy_middleware->setFallbackRequestHandler($this->handler);

        self::assertSame($this->response, $lazy_middleware->process($this->request, $this->handler));
    }
}
