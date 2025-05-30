<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Warmup;

interface StaticCacheDataProvider
{
    /** @return iterable<StaticCacheDataStruct> */
    public function getStaticCacheData(): iterable;
}
