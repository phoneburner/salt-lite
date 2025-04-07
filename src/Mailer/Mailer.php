<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Mailer;

use PhoneBurner\SaltLite\Attribute\Usage\Contract;
use PhoneBurner\SaltLite\Mailer\MailableMessage;

#[Contract]
interface Mailer
{
    public function send(MailableMessage $message): void;
}
