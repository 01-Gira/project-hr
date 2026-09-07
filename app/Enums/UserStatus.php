<?php

namespace App\Enums;

/**
 * Lifecycle state of an authentication identity.
 *
 * Distinct from employment status, which belongs to employee_profiles
 * (01_ERD.md §5: a user is an authentication identity, an employee profile is a
 * workforce identity, and a user may exist without one). Disabling a login must
 * not imply anything about employment, or vice versa.
 */
enum UserStatus: string
{
    case Active = 'ACTIVE';
    case Inactive = 'INACTIVE';
    case Suspended = 'SUSPENDED';

    /**
     * Whether a user in this state may authenticate.
     */
    public function canAuthenticate(): bool
    {
        return $this === self::Active;
    }
}
