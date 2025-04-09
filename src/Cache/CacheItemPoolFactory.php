<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use Psr\Cache\CacheItemPoolInterface;

#[Contract]
interface CacheItemPoolFactory
{
    public function make(CacheDriver $driver, string|null $namespace = null): CacheItemPoolInterface;
}
