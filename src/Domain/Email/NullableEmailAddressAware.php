<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Domain\Email;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Domain\Email\EmailAddress;

#[Contract]
interface NullableEmailAddressAware
{
    public function getEmailAddress(): EmailAddress|null;
}
