<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Clock;

use Carbon\CarbonImmutable;
use PhoneBurner\SaltLite\Clock\Clock;

class SystemClock implements Clock
{
    #[\Override]
    public function now(): CarbonImmutable
    {
        return CarbonImmutable::now();
    }
}
