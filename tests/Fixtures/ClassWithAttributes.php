<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Tests\Fixtures;

#[TestAttribute('class')]
class ClassWithAttributes
{
    public const string CONSTANT = 'constant';

    #[TestAttribute('property')]
    public string $property = '';

    #[TestAttribute('method')]
    public function method(): void
    {
    }
}
