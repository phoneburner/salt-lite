<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Container\ServiceFactory;

use PhoneBurner\SaltLite\App\App;
use PhoneBurner\SaltLite\Container\ServiceFactory\NewInstanceServiceFactory;
use PhoneBurner\SaltLite\Tests\Fixtures\ServiceFactoryTestClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

final class NewInstanceServiceFactoryTest extends TestCase
{
    private App&MockObject $app;

    protected function setUp(): void
    {
        $this->app = $this->createMock(App::class);
    }

    #[Test]
    public function createsNewInstance(): void
    {
        $factory = new NewInstanceServiceFactory(ServiceFactoryTestClass::class);

        $result = $factory($this->app, ServiceFactoryTestClass::class);

        $this->assertInstanceOf(ServiceFactoryTestClass::class, $result);
        $this->assertSame('default', $result->getValue());
    }

    #[Test]
    public function createsNewInstanceWithConstructorArgs(): void
    {
        $factory = new NewInstanceServiceFactory(ServiceFactoryTestClass::class, ['test value']);

        $result = $factory($this->app, ServiceFactoryTestClass::class);

        $this->assertInstanceOf(ServiceFactoryTestClass::class, $result);
        $this->assertSame('test value', $result->getValue());
    }
}
