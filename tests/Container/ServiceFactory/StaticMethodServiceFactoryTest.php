<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceFactory;

use PhoneBurner\SaltLite\Container\ServiceFactory\StaticMethodServiceFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\ServiceFactoryTestClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class StaticMethodServiceFactoryTest extends TestCase
{
    private ContainerInterface&MockObject $container;

    protected function setUp(): void
    {
        $this->container = $this->createMock(ContainerInterface::class);
    }

    #[Test]
    public function invokesDefaultMakeMethod(): void
    {
        $factory = new StaticMethodServiceFactory(ServiceFactoryTestClass::class);
        $result = $factory($this->container, ServiceFactoryTestClass::class);

        $this->assertInstanceOf(ServiceFactoryTestClass::class, $result);
        $this->assertSame('from make', $result->getValue());
    }

    #[Test]
    public function invokesSpecifiedMethod(): void
    {
        $factory = new StaticMethodServiceFactory(ServiceFactoryTestClass::class, 'create');
        $result = $factory($this->container, ServiceFactoryTestClass::class);

        $this->assertInstanceOf(ServiceFactoryTestClass::class, $result);
        $this->assertSame('from create', $result->getValue());
    }
}
