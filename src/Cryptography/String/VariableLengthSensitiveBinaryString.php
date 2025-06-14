<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\String;

use PhoneBurner\SaltLite\Cryptography\ConstantTime;
use PhoneBurner\SaltLite\Cryptography\Exception\CryptoLogicException;
use PhoneBurner\SaltLite\Cryptography\Util;
use PhoneBurner\SaltLite\String\BinaryString\BinaryString;
use PhoneBurner\SaltLite\String\BinaryString\ImportableBinaryString;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringExportBehavior;
use PhoneBurner\SaltLite\String\BinaryString\Traits\BinaryStringImportBehavior;

class VariableLengthSensitiveBinaryString implements ImportableBinaryString
{
    use BinaryStringExportBehavior;
    use BinaryStringImportBehavior;

    private string $bytes;

    final public function __construct(#[\SensitiveParameter] BinaryString|string $bytes)
    {
        $this->bytes = Util::bytes($bytes);
    }

    /**
     * Overwrite the key in memory with null bytes and internally set the value
     * to null when the object is destroyed. This is to prevent the key from leaking
     * into memory dumps or overflows. Doing this requires that the class not be
     * marked as readonly.
     */
    public function __destruct()
    {
        /** @phpstan-ignore isset.initializedProperty */
        if (isset($this->bytes)) {
            /** @phpstan-ignore assign.propertyType */
            \sodium_memzero($this->bytes);
        }
    }

    /**
     * The return value should always be a string, but there is the technical
     * possibility that it could be null if the object destructor is called
     * manually before the object is cleaned up by the runtime.
     */
    public function bytes(): string
    {
        /** @phpstan-ignore nullCoalesce.initializedProperty () */
        return $this->bytes ?? throw CryptoLogicException::unreachable();
    }

    public function length(): int
    {
        return \strlen($this->bytes);
    }

    public function __toString(): string
    {
        return $this->export(static::DEFAULT_ENCODING);
    }

    public function __serialize(): array
    {
        return [$this->export(static::DEFAULT_ENCODING)];
    }

    /**
     * @param list<string> $data
     */
    public function __unserialize(array $data): void
    {
        $this->bytes = ConstantTime::decode(static::DEFAULT_ENCODING, $data[0]);
    }

    public function jsonSerialize(): string
    {
        return $this->export(static::DEFAULT_ENCODING);
    }
}
