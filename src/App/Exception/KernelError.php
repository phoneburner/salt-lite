<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\App\Exception;

use LogicException;
use PhoneBurner\SaltLite\App\Exception\BootError;

class KernelError extends LogicException implements BootError
{
}
