<?php

namespace App\Enums;

/**
 * The default roles from 00_PROJECT_CONTEXT.md §5.
 *
 * Named RoleName rather than Role to avoid colliding with
 * Spatie\Permission\Models\Role wherever both are used.
 *
 * These are seeded defaults, not a closed set — the role list is explicitly
 * configurable. Business behaviour must never be keyed off these names
 * (00 §3.6); they exist only to group permissions.
 */
enum RoleName: string
{
    case SuperAdmin = 'super-admin';
    case Owner = 'owner';
    case HrAdmin = 'hr-admin';
    case Manager = 'manager';
    case Finance = 'finance';
    case AttendanceOperator = 'attendance-operator';
    case Employee = 'employee';
    case Auditor = 'auditor';

    /**
     * Whether this role receives every permission, including ones added later.
     */
    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }
}
