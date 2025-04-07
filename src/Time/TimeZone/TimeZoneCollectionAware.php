<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Time\TimeZone;

use PhoneBurner\SaltLite\Time\TimeZone\TimeZoneCollection;

interface TimeZoneCollectionAware
{
    public function getTimeZones(): TimeZoneCollection;
}
