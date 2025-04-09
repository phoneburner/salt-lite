<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Cryptography\Exception;

use PhoneBurner\SaltLite\Cryptography\Exception\InvalidKey;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class InvalidKeyTest extends TestCase
{
    #[Test]
    public function happyPathTestLength(): void
    {
        self::assertSame('Key Must Be Exactly 16 Bytes', InvalidKey::length(16)->getMessage());
    }
}
