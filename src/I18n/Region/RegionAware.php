<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\I18n\Region;

use PhoneBurner\SaltLite\I18n\Region\Region;

interface RegionAware
{
    public function getRegion(): Region;
}
