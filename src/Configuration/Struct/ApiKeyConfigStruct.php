<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Configuration\Struct;

use PhoneBurner\SaltLite\Configuration\ConfigStruct;
use PhoneBurner\SaltLite\Type\Cast\NonEmptyNullableCast;

/**
 * General purpose configuration struct for something like an API key, enforcing
 * either a non-empty string or null value.
 */
abstract readonly class ApiKeyConfigStruct implements ConfigStruct
{
    use ConfigStructArrayAccess;
    use ConfigStructSerialization;

    /**
     * @var non-empty-string|null
     */
    public string|null $api_key;

    public function __construct(#[\SensitiveParameter] string|null $api_key)
    {
        $this->api_key = NonEmptyNullableCast::string($api_key);
    }
}
