<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode\AreaCode;
use PhoneBurner\SaltLite\Domain\PhoneNumber\AreaCode\NullableAreaCodeAware;

#[Contract]
interface AreaCodeAware extends NullableAreaCodeAware
{
    public function getAreaCode(): AreaCode;
}
