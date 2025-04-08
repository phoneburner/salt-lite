<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

use Psr\Log\LoggerInterface;

class MethodFixture
{
    public function method_with_parameters(mixed $first, mixed $second): void
    {
    }

    public function method_with_type_hint(LoggerInterface $logger): void
    {
    }

    public function method_with_default_value(mixed $param = 'default'): void
    {
    }

    public function method_with_default_and_type(LoggerInterface|null $logger = null): void
    {
    }

    public function method_with_union_type(string|int $param): void
    {
    }

    public function method_with_builtin_type(string $param): void
    {
    }

    public function method_with_self_type(self $param): void
    {
    }
}
