<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Uuid;

use PhoneBurner\SaltLite\Uuid\UuidStringWrapper;
use Ramsey\Uuid\UuidInterface;

class UuidString implements UuidInterface
{
    use UuidStringWrapper;
}
