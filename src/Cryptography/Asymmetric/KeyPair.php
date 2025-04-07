<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Asymmetric;

use PhoneBurner\SaltLite\Cryptography\KeyManagement\Key;
use PhoneBurner\SaltLite\Cryptography\String\BinaryString;

interface KeyPair extends Key
{
    public function secret(): SecretKey;

    public function public(): PublicKey;

    public static function generate(): static;

    public static function fromSeed(BinaryString $seed): static;
}
