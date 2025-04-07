<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode\AreaCode;

#[Contract]
interface NullableAreaCodeAware
{
    public function getAreaCode(): AreaCode|null;
}
