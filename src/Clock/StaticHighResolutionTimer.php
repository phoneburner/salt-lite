<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Clock;

use PhoneBurner\SaltLite\Clock\HighResolutionTimer;

class StaticHighResolutionTimer implements HighResolutionTimer
{
    public function __construct(private readonly int $now)
    {
    }

    #[\Override]
    public function now(): int
    {
        return $this->now;
    }
}
