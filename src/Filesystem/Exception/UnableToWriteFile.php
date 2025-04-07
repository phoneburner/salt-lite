<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Filesystem\Exception;

class UnableToWriteFile extends \RuntimeException
{
    public static function atLocation(string $location, string $reason = '', \Throwable|null $previous = null): self
    {
        return new self(\rtrim("Unable to write file at location: {$location}. {$reason}"), previous: $previous);
    }
}
