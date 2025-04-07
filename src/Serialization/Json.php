<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Serialization;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class Json
{
    use HasNonInstantiableBehavior;

    public static function encode(mixed $value, int $flags = 0): string
    {
        $json = \json_encode($value, $flags | \JSON_THROW_ON_ERROR);
        if ($json === false) {
            throw new \JsonException('Failed to encode JSON');
        }

        return $json;
    }

    public static function decode(string $json, int $flags = 0): array
    {
        $value = \json_decode($json, true, 512, $flags | \JSON_THROW_ON_ERROR);
        if (! \is_array($value)) {
            throw new \JsonException('Failed to decode JSON into array');
        }

        return $value;
    }

    public function validate(string $json, bool $ignore_invalid_utf8_chars = false): bool
    {
        return \json_validate($json, 512, $ignore_invalid_utf8_chars ? \JSON_INVALID_UTF8_IGNORE : 0);
    }
}
