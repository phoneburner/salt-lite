<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

enum Stoplight: string
{
    case Green = 'green';
    case Yellow = 'yellow';
    case Red = 'red';
}
