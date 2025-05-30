<?php

declare(strict_types=1);

namespace PhoneBurner\SaltLite\Cryptography\Paseto\Claims;

use PhoneBurner\SaltLite\Enum\WithValuesStaticMethod;

/**
 * The following keys are reserved for use within PASETO footers, when the footer
 * is a JSON object. Users SHOULD NOT write arbitrary/invalid data to any keys below
 * when in the top level of the footer.
 */
enum RegisteredFooterClaim: string
{
    use WithValuesStaticMethod;

    case KeyId = 'kid'; // string
    case WrappedPaserk = 'wpk'; // string
}
