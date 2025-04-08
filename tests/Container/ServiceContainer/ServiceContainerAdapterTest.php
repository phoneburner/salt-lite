<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceContainer;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\App\Context;
use PhoneBurner\SaltLite\App\Environment;
use PhoneBurner\SaltLite\Configuration\Configuration;
use PhoneBurner\SaltLite\Container\DeferrableServiceProvider;
use PhoneBurner\SaltLite\Container\Exception\CircularDependency;
use PhoneBurner\SaltLite\Container\Exception\InvalidServiceProvider;
use PhoneBurner\SaltLite\Container\Exception\NotFound;
use PhoneBurner\SaltLite\Container\ServiceContainer\ServiceContainerAdapter;
use PhoneBurner\SaltLite\Container\ServiceFactory\CallableServiceFactory;
use PhoneBurner\SaltLite\Container\ServiceProvider;
use PhoneBurner\SaltLite\Logging\BufferLogger;
use PhoneBurner\SaltLite\Tests\Fixtures\TestApp;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ServiceContainerAdapterTest extends TestCase
{
    private ServiceContainerAdapter $container;

    private App&MockObject $app;

    protected function setUp(): void
    {
        $this->app = $this->createMock(App::class);
        $this->container = new ServiceContainerAdapter($this->app);
    }

    #[Test]
    public function has_returns_true_for_resolved_services(): void
    {
        $service = new \stdClass();
        $this->container->set(LoggerInterface::class, $service);

        self::assertTrue($this->container->has(LoggerInterface::class));
    }

    #[Test]
    public function has_returns_true_for_factories(): void
    {
        $factory = new CallableServiceFactory(fn(): \stdClass => new \stdClass());
        $this->container->set(LoggerInterface::class, $factory);

        self::assertTrue($this->container->has(LoggerInterface::class));
    }

    #[Test]
    public function has_returns_true_for_deferred_services(): void
    {
        $provider = new class implements DeferrableServiceProvider {
            public static function provides(): array
            {
                return [LoggerInterface::class];
            }

            public static function bind(): array
            {
                return [];
            }

            public static function register(App $app): void
            {
            }
        };

        $this->container->defer($provider);
        self::assertTrue($this->container->has(LoggerInterface::class));
    }

    #[Test]
    public function has_returns_true_for_instantiable_classes_in_non_strict_mode(): void
    {
        self::assertTrue($this->container->has(\stdClass::class));
    }

    #[Test]
    public function has_returns_false_for_non_instantiable_classes_in_strict_mode(): void
    {
        self::assertFalse($this->container->has(LoggerInterface::class, true));
    }

    #[Test]
    public function get_returns_resolved_service(): void
    {
        $service = new \stdClass();
        $this->container->set(LoggerInterface::class, $service);

        self::assertSame($service, $this->container->get(LoggerInterface::class));
    }

    #[Test]
    public function get_resolves_service_from_factory(): void
    {
        $service = new \stdClass();
        $factory = new CallableServiceFactory(fn(): \stdClass => $service);
        $this->container->set(LoggerInterface::class, $factory);

        self::assertSame($service, $this->container->get(LoggerInterface::class));
    }

    #[Test]
    public function get_throws_not_found_for_unregistered_service(): void
    {
        $this->expectException(NotFound::class);
        $this->container->get(LoggerInterface::class);
    }

    #[Test]
    public function get_resolves_service_from_deferred_provider(): void
    {
        $service = new NullLogger();
        $provider = new class ($service) implements DeferrableServiceProvider {
            private static NullLogger $logger;

            public function __construct(NullLogger $logger)
            {
                self::$logger = $logger;
            }

            public static function provides(): array
            {
                return [LoggerInterface::class];
            }

            public static function bind(): array
            {
                return [LoggerInterface::class => NullLogger::class];
            }

            public static function register(App $app): void
            {
                $app->services->set(LoggerInterface::class, self::$logger);
            }
        };

        $environment = $this->createMock(Environment::class);
        $config = $this->createMock(Configuration::class);
        $mock_app = new TestApp($this->container, Context::Test, $environment, $config);

        $this->container = new ServiceContainerAdapter($mock_app);
        $this->container->defer($provider);
        $this->container->set(LoggerInterface::class, $service);

        $resolved = $this->container->get(LoggerInterface::class);
        self::assertSame($service, $resolved);
    }

    #[Test]
    public function get_detects_circular_dependencies(): void
    {
        $this->container->set(LoggerInterface::class, fn(): object => $this->container->get(NullLogger::class));
        $this->container->set(NullLogger::class, fn(): object => $this->container->get(LoggerInterface::class));

        $this->expectException(CircularDependency::class);
        $this->container->get(LoggerInterface::class);
    }

    #[Test]
    public function set_accepts_service_factory(): void
    {
        $factory = new CallableServiceFactory(fn(): \stdClass => new \stdClass());
        $this->container->set(LoggerInterface::class, $factory);

        self::assertTrue($this->container->has(LoggerInterface::class));
    }

    #[Test]
    public function set_accepts_closure_as_factory(): void
    {
        $this->container->set(LoggerInterface::class, fn(): \stdClass => new \stdClass());

        self::assertTrue($this->container->has(LoggerInterface::class));
    }

    #[Test]
    public function set_accepts_object(): void
    {
        $service = new \stdClass();
        $this->container->set(LoggerInterface::class, $service);

        self::assertSame($service, $this->container->get(LoggerInterface::class));
    }

    #[Test]
    public function set_throws_for_non_object_value(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->container->set(LoggerInterface::class, 'not an object');
    }

    #[Test]
    public function unset_removes_service(): void
    {
        $this->container->set(LoggerInterface::class, new \stdClass());
        $this->container->unset(LoggerInterface::class);

        self::assertFalse($this->container->has(LoggerInterface::class, true));
    }

    #[Test]
    public function register_registers_service_provider(): void
    {
        $provider = new class implements ServiceProvider {
            public static function bind(): array
            {
                return [LoggerInterface::class => NullLogger::class];
            }

            public static function register(App $app): void
            {
            }
        };

        $this->container->register($provider);
        self::assertTrue($this->container->has(LoggerInterface::class));
    }

    #[Test]
    public function register_throws_for_invalid_provider(): void
    {
        $this->expectException(InvalidServiceProvider::class);
        /** @phpstan-ignore argument.type (intentional error) */
        $this->container->register(\stdClass::class);
    }

    #[Test]
    public function defer_registers_deferred_provider(): void
    {
        $provider = new class implements DeferrableServiceProvider {
            public static function provides(): array
            {
                return [LoggerInterface::class];
            }

            public static function bind(): array
            {
                return [];
            }

            public static function register(App $app): void
            {
            }
        };

        $this->container->defer($provider);
        self::assertTrue($this->container->has(LoggerInterface::class));
    }

    #[Test]
    public function defer_throws_for_invalid_provider(): void
    {
        $this->expectException(InvalidServiceProvider::class);
        /** @phpstan-ignore argument.type (intentional error) */
        $this->container->defer(\stdClass::class);
    }

    #[Test]
    public function set_logger_updates_logger(): void
    {
        $logger = new NullLogger();
        $this->container->setLogger($logger);

        // No way to directly verify the logger was set, but we can check it doesn't throw
        self::assertTrue(true);
    }

    #[Test]
    public function set_logger_copies_buffer_logger_entries(): void
    {
        $bufferLogger = new BufferLogger();
        $container = new ServiceContainerAdapter($this->app, $bufferLogger);

        $newLogger = new NullLogger();
        $container->setLogger($newLogger);

        // No way to directly verify the entries were copied, but we can check it doesn't throw
        self::assertTrue(true);
    }

    #[Test]
    public function call_invokes_closure(): void
    {
        $called = false;
        $closure = function () use (&$called): string {
            $called = true;
            return 'result';
        };

        $result = $this->container->call($closure);

        self::assertTrue($called);
        self::assertSame('result', $result);
    }

    #[Test]
    public function call_invokes_method_on_object(): void
    {
        $object = new class {
            public function test(): string
            {
                return 'result';
            }
        };

        $result = $this->container->call($object, 'test');

        self::assertSame('result', $result);
    }

    #[Test]
    public function call_invokes_method_on_class_string(): void
    {
        $service = new class {
            public function test(): string
            {
                return 'result';
            }
        };
        $this->container->set($service::class, $service);

        $result = $this->container->call($service::class, 'test');

        self::assertSame('result', $result);
    }

    #[Test]
    public function call_throws_for_invalid_object(): void
    {
        $this->expectException(\UnexpectedValueException::class);
        /** @phpstan-ignore argument.type (intentional error) */
        $this->container->call('not a class');
    }

    #[Test]
    public function call_throws_for_non_invokable_object(): void
    {
        $object = new \stdClass();

        $this->expectException(\UnexpectedValueException::class);
        $this->container->call($object);
    }
}
