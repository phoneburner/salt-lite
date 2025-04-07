<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Logging;

use PhoneBurner\SaltLite\Logging\LogEntry;

interface Loggable
{
    public function getLogEntry(): LogEntry;
}
