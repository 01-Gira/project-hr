<?php

namespace App\Enums;

/**
 * How many rows a viewer may see of a given resource.
 *
 * 07_PERMISSION_MATRIX.md expresses access with cells like "team", "own" and
 * "all". Spatie permissions are flat — they answer whether a capability is held,
 * never over which rows — so ADR-0002 splits the question in two: permissions
 * grant capability, this enum plus a query scope decides reach.
 */
enum ScopeLevel: string
{
    /** Every row. */
    case All = 'ALL';

    /** Rows belonging to the viewer's team: their management chain, branch or department. */
    case Team = 'TEAM';

    /** Only rows belonging to the viewer themselves. */
    case Own = 'OWN';

    /** Nothing. The viewer holds no view capability for this resource. */
    case None = 'NONE';

    public function seesAnything(): bool
    {
        return $this !== self::None;
    }

    /**
     * Whether this level is at least as wide as another.
     */
    public function isAtLeast(self $other): bool
    {
        return $this->rank() >= $other->rank();
    }

    private function rank(): int
    {
        return match ($this) {
            self::None => 0,
            self::Own => 1,
            self::Team => 2,
            self::All => 3,
        };
    }
}
