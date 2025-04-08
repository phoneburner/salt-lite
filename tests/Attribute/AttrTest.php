<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Attribute;

use PhoneBurner\SaltLite\Attribute\Attr;
use PhoneBurner\SaltLite\Tests\Fixtures\ClassWithAttributes;
use PhoneBurner\SaltLite\Tests\Fixtures\TestAttribute;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use ReflectionClass;
use ReflectionMethod;
use ReflectionProperty;

final class AttrTest extends TestCase
{
    #[Test]
    public function find_returns_attributes_on_class(): void
    {
        $attributes = Attr::find(ClassWithAttributes::class, TestAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(TestAttribute::class, $attributes[0]);
        self::assertSame('class', $attributes[0]->name);
    }

    #[Test]
    public function find_returns_attributes_on_reflection_class(): void
    {
        $reflection = new ReflectionClass(ClassWithAttributes::class);
        $attributes = Attr::find($reflection, TestAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(TestAttribute::class, $attributes[0]);
        self::assertSame('class', $attributes[0]->name);
    }

    #[Test]
    public function find_returns_attributes_on_reflection_method(): void
    {
        $reflection = new ReflectionMethod(ClassWithAttributes::class, 'method');
        $attributes = Attr::find($reflection, TestAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(TestAttribute::class, $attributes[0]);
        self::assertSame('method', $attributes[0]->name);
    }

    #[Test]
    public function find_returns_attributes_on_reflection_property(): void
    {
        $reflection = new ReflectionProperty(ClassWithAttributes::class, 'property');
        $attributes = Attr::find($reflection, TestAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(TestAttribute::class, $attributes[0]);
        self::assertSame('property', $attributes[0]->name);
    }

    #[Test]
    public function find_returns_attributes_on_object(): void
    {
        $object = new ClassWithAttributes();
        $attributes = Attr::find($object, TestAttribute::class);
        self::assertCount(1, $attributes);
        self::assertInstanceOf(TestAttribute::class, $attributes[0]);
        self::assertSame('class', $attributes[0]->name);
    }

    #[Test]
    public function find_returns_empty_array_when_no_attributes_found(): void
    {
        $attributes = Attr::find(\stdClass::class, TestAttribute::class);
        self::assertCount(0, $attributes);
    }

    #[Test]
    public function first_returns_first_attribute(): void
    {
        $attribute = Attr::first(ClassWithAttributes::class, TestAttribute::class);
        self::assertInstanceOf(TestAttribute::class, $attribute);
        self::assertSame('class', $attribute->name);
    }

    #[Test]
    public function first_returns_null_when_no_attributes_found(): void
    {
        $attribute = Attr::first(\stdClass::class, TestAttribute::class);
        self::assertNull($attribute);
    }

    #[Test]
    #[DataProvider('invalid_reflector_provider')]
    public function find_throws_exception_for_invalid_reflector(mixed $invalid_reflector): void
    {
        $this->expectException(\UnexpectedValueException::class);
        Attr::find($invalid_reflector);
    }

    public static function invalid_reflector_provider(): \Iterator
    {
        $reflector = new class implements \Reflector {
            public function __toString(): string
            {
                return 'Invalid reflector';
            }

            public static function export(): null
            {
                // TODO: compatible with < PHP 8.0 definitions
                return null;
            }
        };

        yield 'custom reflector' => [$reflector];
    }
}
