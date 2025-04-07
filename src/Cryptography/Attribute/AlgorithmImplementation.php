<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Attribute;

use PhoneBurner\SaltLite\Cryptography\Asymmetric\AsymmetricEncryptionAlgorithm as AsymmetricEncryptionAlgorithm;
use PhoneBurner\SaltLite\Cryptography\Symmetric\SymmetricEncryptionAlgorithm as SymmetricEncryptionAlgorithm;

#[\Attribute]
final readonly class AlgorithmImplementation
{
    public function __construct(public AsymmetricEncryptionAlgorithm|SymmetricEncryptionAlgorithm $algorithm)
    {
    }
}
