<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Session\Exception;

use PhoneBurner\SaltLite\Http\Session\Exception\HttpSessionException;

class InvalidSessionId extends \UnexpectedValueException implements HttpSessionException
{
}
