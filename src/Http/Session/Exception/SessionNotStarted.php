<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Http\Session\Exception;

use PhoneBurner\SaltLite\Http\Session\Exception\HttpSessionException;

class SessionNotStarted extends \LogicException implements HttpSessionException
{
}
