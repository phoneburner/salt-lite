<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cache\Exception;

use PhoneBurner\SaltLite\Cache\Exception\CacheException;

class LockAcquisitionTimeout extends \RuntimeException implements CacheException
{
}
