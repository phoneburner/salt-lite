<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Http;

use PhoneBurner\SaltLite\Http\RequestHandlerFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\TestContainer;
use PhoneBurner\SaltLite\Tests\Fixtures\TestRequestHandler;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class RequestHandlerFactoryTest extends TestCase
{
    private TestContainer $container;
    private RequestHandlerFactory $factory;

    protected function setUp(): void
    {
        $this->container = new TestContainer();
        $this->factory = new RequestHandlerFactory($this->container);
    }

    #[Test]
    public function make_returns_handler_when_given_instance(): void
    {
        $handler = new TestRequestHandler();

        $result = $this->factory->make($handler);

        self::assertSame($handler, $result);
    }

    #[Test]
    public function make_resolves_from_container_when_given_class_name(): void
    {
        $handler = new TestRequestHandler();
        $class_name = TestRequestHandler::class;

        $this->container->registerService($class_name, $handler);

        $result = $this->factory->make($class_name);

        self::assertSame($handler, $result);
        self::assertTrue($this->container->wasServiceRequested($class_name));
    }

    #[Test]
    public function make_throws_when_given_invalid_string(): void
    {
        $invalid_class = 'InvalidClass';

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(RequestHandlerFactory::TYPE_ERROR);

        $this->factory->make($invalid_class);
    }

    #[Test]
    public function make_throws_when_given_non_handler_class(): void
    {
        $non_handler_class = \stdClass::class;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(RequestHandlerFactory::TYPE_ERROR);

        $this->factory->make($non_handler_class);
    }
}
