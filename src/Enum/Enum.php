<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Enum;

use PhoneBurner\SaltLite\Trait\HasNonInstantiableBehavior;

final readonly class Enum
{
    use HasNonInstantiableBehavior;

    public static function values(\BackedEnum ...$enum): array
    {
        return \array_column($enum, 'value');
    }
}
