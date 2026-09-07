<?php

namespace App\Contracts;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

/**
 * A model whose rows are visible to different viewers in different amounts.
 *
 * ADR-0002. Every list query over a scoped resource must go through
 * scopeVisibleTo. A bare Model::all() or an unscoped paginate() on one of these
 * models is a data leak, not a style problem — the permission check upstream
 * only established that the viewer may see *something* of this type.
 *
 * Implemented from Phase 1 onward by EmployeeProfile, Attendance, LeaveRequest,
 * EmployeeSalary and the reporting models. The contract exists in Phase 0 so
 * those phases implement one agreed mechanism instead of inventing five.
 */
interface ScopedToViewer
{
    /**
     * Constrain the query to the rows this viewer is permitted to see.
     *
     * @param  Builder<covariant static>  $query
     * @return Builder<covariant static>
     */
    public function scopeVisibleTo(Builder $query, User $viewer): Builder;

    /**
     * The permission family governing this resource, e.g. "attendance",
     * "employees", "leave", "salary".
     *
     * ResolveScopeLevel appends .view-all / .view-team / .view-own to it.
     */
    public static function permissionFamily(): string;
}
