<?php

namespace App\Support\Authorization;

use App\Enums\ScopeLevel;
use App\Models\User;

/**
 * Turns a viewer's flat permissions into a row-visibility level.
 *
 * ADR-0002: 07_PERMISSION_MATRIX.md marks cells "all", "team" and "own", which
 * the permission catalogue encodes as the triplet
 *
 *     <family>.view-all
 *     <family>.view-team
 *     <family>.view-own
 *
 * The widest permission held wins, so a manager who is also granted view-all is
 * not accidentally narrowed to their own team.
 *
 * This deliberately does not consult employee_profiles: that table arrives in
 * Phase 1. Resolving *which* team is the job of the model's scopeVisibleTo;
 * resolving *how wide* the viewer's reach is depends only on permissions and is
 * therefore implementable, and testable, now.
 */
class ResolveScopeLevel
{
    public function handle(User $viewer, string $permissionFamily): ScopeLevel
    {
        return match (true) {
            $viewer->can("{$permissionFamily}.view-all") => ScopeLevel::All,
            $viewer->can("{$permissionFamily}.view-team") => ScopeLevel::Team,
            $viewer->can("{$permissionFamily}.view-own") => ScopeLevel::Own,
            default => ScopeLevel::None,
        };
    }
}
