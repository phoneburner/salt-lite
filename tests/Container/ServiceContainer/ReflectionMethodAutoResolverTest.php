<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceContainer;

use PhoneBurner\SaltLite\Container\Exception\UnableToAutoResolveParameter;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterName;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterPosition;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideByParameterType;
use PhoneBurner\SaltLite\Container\ParameterOverride\OverrideCollection;
use PhoneBurner\SaltLite\Container\ServiceContainer;
use PhoneBurner\SaltLite\Container\ServiceContainer\ReflectionMethodAutoResolver;
use PhoneBurner\SaltLite\Tests\Fixtures\MethodFixture;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

final class ReflectionMethodAutoResolverTest extends TestCase
{
    private ReflectionMethodAutoResolver $resolver;
    private ContainerInterface $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ServiceContainer::class);
        $this->resolver = new ReflectionMethodAutoResolver($this->container);
    }

    #[Test]
    public function resolves_parameter_by_position(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_parameters');
        $parameter = $method->getParameters()[0]; // First parameter: $first

        $override_value = 'position override';
        $override = new OverrideByParameterPosition(0, $override_value);
        $overrides = new OverrideCollection($override);

        $resolver = new ReflectionMethodAutoResolver($this->container, $overrides);

        self::assertSame($override_value, $resolver->__invoke($parameter));
    }

    #[Test]
    public function resolves_parameter_by_name(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_parameters');
        $parameter = $method->getParameters()[1]; // Second parameter: $second

        $override_value = 'name override';
        $override = new OverrideByParameterName('second', $override_value);
        $overrides = new OverrideCollection($override);

        $resolver = new ReflectionMethodAutoResolver($this->container, $overrides);

        self::assertSame($override_value, $resolver->__invoke($parameter));
    }

    #[Test]
    public function resolves_parameter_by_type(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_type_hint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();
        $override = new OverrideByParameterType(LoggerInterface::class, $logger);
        $overrides = new OverrideCollection($override);

        $resolver = new ReflectionMethodAutoResolver($this->container, $overrides);

        self::assertSame($logger, $resolver->__invoke($parameter));
    }

    #[Test]
    public function resolves_parameter_from_container(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_type_hint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();

        $container = $this->createMock(ServiceContainer::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class, true)
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertSame($logger, $resolver->__invoke($parameter));
    }

    #[Test]
    public function uses_default_value_when_no_type_and_default_available(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_default_value');
        $parameter = $method->getParameters()[0]; // Parameter: $param = 'default'

        self::assertSame('default', $this->resolver->__invoke($parameter));
    }

    #[Test]
    public function throws_when_no_type_and_no_default_available(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_parameters');
        $parameter = $method->getParameters()[0]; // Parameter: $first

        $this->expectException(UnableToAutoResolveParameter::class);
        $this->resolver->__invoke($parameter);
    }

    #[Test]
    public function prefers_default_value_over_autowiring(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_default_and_type');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger = null

        // Container should not be called
        $container = $this->createMock(ServiceContainer::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class, true)
            ->willReturn(false);
        $container->expects($this->never())
            ->method('get');

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertNull($resolver->__invoke($parameter));
    }

    #[Test]
    public function falls_back_to_container_resolve_when_no_other_options(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_type_hint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();

        $container = $this->createMock(ServiceContainer::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class, true)
            ->willReturn(false);
        $container->expects($this->once())
            ->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertSame($logger, $resolver->__invoke($parameter));
    }

    #[Test]
    public function handles_non_named_type(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_union_type');
        $parameter = $method->getParameters()[0]; // Parameter: string|int $param

        $this->expectException(UnableToAutoResolveParameter::class);
        $this->resolver->__invoke($parameter);
    }

    #[Test]
    public function handles_builtin_type(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_builtin_type');
        $parameter = $method->getParameters()[0]; // Parameter: string $param

        $this->expectException(UnableToAutoResolveParameter::class);
        $this->resolver->__invoke($parameter);
    }

    #[Test]
    public function handles_self_type(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_self_type');
        $parameter = $method->getParameters()[0]; // Parameter: self $param

        $this->expectException(UnableToAutoResolveParameter::class);
        $this->resolver->__invoke($parameter);
    }

    #[Test]
    public function supports_non_service_container(): void
    {
        $method = new \ReflectionMethod(MethodFixture::class, 'method_with_type_hint');
        $parameter = $method->getParameters()[0]; // Parameter: LoggerInterface $logger

        $logger = new NullLogger();

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('has')
            ->with(LoggerInterface::class)
            ->willReturn(true);
        $container->expects($this->once())
            ->method('get')
            ->with(LoggerInterface::class)
            ->willReturn($logger);

        $resolver = new ReflectionMethodAutoResolver($container);

        self::assertSame($logger, $resolver->__invoke($parameter));
    }
}
