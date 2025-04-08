<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

#[\Attribute(\Attribute::TARGET_CLASS | \Attribute::TARGET_METHOD | \Attribute::TARGET_PROPERTY)]
class TestAttribute
{
    public function __construct(public string $name = 'test')
    {
    }
}
