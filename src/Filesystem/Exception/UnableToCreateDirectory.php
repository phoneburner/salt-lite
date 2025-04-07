<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Filesystem\Exception;

class UnableToCreateDirectory extends \RuntimeException
{
    public static function atLocation(string $location, string $reason = '', \Throwable|null $previous = null): self
    {
        return new self(\rtrim("Unable to create directory at location: {$location}. {$reason}"), previous: $previous);
    }
}
