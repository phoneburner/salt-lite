<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Serialization;

use PhoneBurner\SaltLite\Serialization\Json;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class JsonTest extends TestCase
{
    #[Test]
    public function encode_returns_json_string(): void
    {
        $data = ['foo' => 'bar', 'baz' => 123, 'nested' => ['qux' => true]];
        $expected = '{"foo":"bar","baz":123,"nested":{"qux":true}}';

        self::assertSame($expected, Json::encode($data));
    }

    #[Test]
    public function encode_applies_flags(): void
    {
        $data = ['foo' => 'bar'];
        $result = Json::encode($data, \JSON_PRETTY_PRINT);

        self::assertStringContainsString("\n", $result);
        self::assertStringContainsString("    ", $result);
    }

    #[Test]
    public function encode_throws_on_invalid_structure(): void
    {
        // Create an object with circular reference
        $a = new \stdClass();
        $b = new \stdClass();
        $a->b = $b;
        $b->a = $a;

        $this->expectException(\JsonException::class);

        Json::encode($a, 0);
    }

    #[Test]
    public function encode_throws_on_invalid_data(): void
    {
        $data = "\xB1\x31"; // Invalid UTF-8 sequence

        $this->expectException(\JsonException::class);

        Json::encode($data, 0);
    }

    #[Test]
    public function decode_returns_array(): void
    {
        $json = '{"foo":"bar","baz":123,"nested":{"qux":true}}';
        $expected = ['foo' => 'bar', 'baz' => 123, 'nested' => ['qux' => true]];

        $result = Json::decode($json);

        self::assertEquals($expected, $result);
    }

    #[Test]
    public function decode_applies_flags(): void
    {
        // Test with JSON_THROW_ON_ERROR flag
        $json = '{"foo":"bar",}'; // Invalid JSON with trailing comma

        $this->expectException(\JsonException::class);

        Json::decode($json);
    }

    #[Test]
    public function decode_throws_when_result_is_not_array(): void
    {
        $json = '"not an array"';

        $this->expectException(\JsonException::class);
        $this->expectExceptionMessage('Failed to decode JSON into array');

        Json::decode($json);
    }

    public function validate_returns_true_for_valid_json(): void
    {
        $json = '{"foo":"bar","baz":123}';
        self::assertTrue(Json::validate($json));
    }

    #[Test]
    public function validate_returns_false_for_invalid_json(): void
    {
        $json = '{"foo":"bar",}'; // Invalid JSON with trailing comma
        self::assertFalse(Json::validate($json));
    }
}
